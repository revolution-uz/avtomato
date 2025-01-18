<?php

namespace GraphQL\Ecommerce\Common\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsProviderEnum;
use App\Models\BaseFeature;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\Feature;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\SmsFaker;
use Tests\GraphQL\GraphQLTestCase;

class SendOtpGraphQLMutationTest extends GraphQLTestCase
{
    #[Test]
    public function successSendOtp()
    {
        SmsFaker::sayqal();
        Http::fake();

        $phone = '998991234567';
        $baseFeature = BaseFeature::where('name', FeatureEnum::SMS)->firstOrFail();
        $baseFeature->update([
            'original_data' => ['provider' => SmsProviderEnum::SMS_SAYQAL, 'username' => 'username', 'secret' => 'secret'],
        ]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['phone' => $phone])
        );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['ecommerceSendOtp']]);
        $this->assertTrue($response->json('data.ecommerceSendOtp'));
        $confirmationSms = CardConfirmationSms::query()->where('phone', $phone)->firstOrFail();
        Http::assertSent(fn ($request) => $request->url() === 'https://routee.sayqal.uz/sms/TransmitSMS'
            && $request['message']['phone'] === $phone
            && $request['message']['text'] === "Ecommerce dan ro'yxatdan o'tish uchun kod: {$confirmationSms->code}"
        );
    }

    #[Test]
    public function errorSendOtpWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['phone' => '998991234567'])
        );

        $response->assertStatus(403);
    }

    #[Test]
    public function errorSendOtpWithInactiveFeature(): void
    {
        Feature::findByName(FeatureEnum::ECOMMERCE)->update(['is_active' => false]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['phone' => '998991234567'])
        );

        $response->assertStatus(403);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery()
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$phone" of required type "String!" was not provided.'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['phone' => Str::random()])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'Телефон', 'digits' => 12]), $response->json('errors.0.extensions.validation.phone.0'));
        $this->assertEquals('The phone is invalid phone number.', $response->json('errors.0.extensions.validation.phone.1'));
    }

    public function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation EcommerceSendOtp($phone: String!) {
                    ecommerceSendOtp(phone: $phone)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
