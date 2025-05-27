<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\Company\InstallmentStatusEnum;
use App\Helpers\Utilities;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UniredControllerTest extends TestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;
    private array $_installmentInput;
    private string $_token;
    private array $_webHookInput;
    private array $_checkPaymentInput;
    private array $_sendSmsInput;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_token = config('clients.unired.pinfl.callback-token');
        $this->_customer = Customer::factory()->create();

        $this->_installmentInput = [
            'amount' => 1000000,
            'percent' => 15,
            'initial_fee' => 100000,
            'period' => 3,
            'starts_in' => Carbon::now(),
            'status' => InstallmentStatusEnum::ACTIVE,
            'customer_id' => $this->_customer->id,
        ];

        $this->_webHookInput = [
            'pinfl' => '33012933450133',
            'loan_id' => Utilities::generateIdByTenancy($this->_customer->id),
            'transaction_id' => '015397909580',
            'ext_id' => 'autopay_506416_165907797412',
            'card_mask' => '860048******6728',
            'card_owner' => 'GOFUROV SHAXZODBEK',
            'amount' => 700000,
            'date' => '2022-07-29',
            'terminal' => '92412344',
            'merchant' => '90489053',
            'filial_id' => '2',
            'type' => 'Online',
            'status' => 'OK',
            'refunded_at' => '2022-07-29 12:01:39',
            'created_at' => '2022-07-29T06:59:34.000000Z',
            'is_sent' => 1,
        ];

        $this->_checkPaymentInput = [
            'loan_id' => Utilities::generateIdByTenancy($this->_customer->id),
            'old_debt' => 100000,
        ];

        $this->_sendSmsInput = [
            'messages' => [
                [
                    'recipient' => '998973*****',
                    'message-id' => 'abc000000001',
                    'sms' => [
                        'originator' => '3700',
                        'content' => [
                            'text' => 'Test message',
                        ],
                    ],
                ],
            ],
            'params' => [
                'phone' => '998973******',
                'content' => 'Test Message',
                'pan' => '8600***',
                'loan_id' => Utilities::generateIdByTenancy($this->_customer->id),
                'ref_num' => '00000000',
                'ext' => '01bcf9f92ffd432ca06ce2f5d2c89ef2',
                'amount' => 800,
                'merchant_id' => '9975612202',
                'terminal_id' => '236103662',
                'date' => 'Y-m-d',
            ],
        ];

        Artisan::call('tenants:seed', ['--class' => TransactionTypeSeeder::class, '--tenants' => [tenant('id')]]);
    }

    #[Test]
    public function successForAnInstallment(): void
    {
        $installment = Installment::factory()->create($this->_installmentInput);
        $amount = $installment->debtAmount;

        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->postJson(
                route('uniredCallback.webHook'),
                array_merge($this->_webHookInput, ['amount' => $amount * 100])
            );

        $response->assertOk();
        $this->assertArrayHasKey('success', $response->json());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertTrue($response->json('success'));
        $this->assertEquals('OK', $response->json('message'));

        $installment->refresh();
        $transaction = Transaction::query()->first();
        $this->assertEquals(TransactionType::UNIRED_WITH_PINFL, $transaction->transaction_type_id);
        $this->assertEquals($this->_webHookInput['ext_id'], $transaction->external_id);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertContains($transaction->installment_id, $this->_customer->installments->pluck('id'));
        $this->assertEquals($this->_customer->id, $transaction->customer_id);
        $this->assertEquals($this->_webHookInput['card_mask'], $transaction->card_pan);
        $this->assertEquals($this->_webHookInput['created_at'], $transaction->paid_at?->format('Y-m-d\TH:i:s.u\Z'));

        $this->assertEquals(0, $installment->debtAmount);
    }

    #[Test]
    public function successForMultiInstallments(): void
    {
        Installment::factory()->count(2)->create($this->_installmentInput);

        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->postJson(
                route('uniredCallback.webHook'),
                array_merge($this->_webHookInput, ['amount' => 445000 * 100])
            );

        $response->assertOk();
        $this->assertArrayHasKey('success', $response->json());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertTrue($response->json('success'));
        $this->assertEquals('OK', $response->json('message'));

        $this->assertEquals(2, Transaction::query()->count());

        $transaction = Transaction::query()->firstWhere('amount', 345000);
        $this->assertNotNull($transaction);
        $this->assertEquals(TransactionType::UNIRED_WITH_PINFL, $transaction->transaction_type_id);
        $this->assertEquals($this->_webHookInput['ext_id'], $transaction->external_id);
        $this->assertContains($transaction->installment_id, $this->_customer->installments->pluck('id'));
        $this->assertEquals($this->_customer->id, $transaction->customer_id);
        $this->assertEquals($this->_webHookInput['card_mask'], $transaction->card_pan);
        $this->assertEquals($this->_webHookInput['created_at'], $transaction->paid_at?->format('Y-m-d\TH:i:s.u\Z'));

        $transaction = Transaction::query()->firstWhere('amount', 100000);
        $this->assertNotNull($transaction);
        $this->assertEquals(TransactionType::UNIRED_WITH_PINFL, $transaction->transaction_type_id);
        $this->assertEquals($this->_webHookInput['ext_id'], $transaction->external_id);
        $this->assertContains($transaction->installment_id, $this->_customer->installments->pluck('id'));
        $this->assertEquals($this->_customer->id, $transaction->customer_id);
        $this->assertEquals($this->_webHookInput['card_mask'], $transaction->card_pan);
        $this->assertEquals($this->_webHookInput['created_at'], $transaction->paid_at?->format('Y-m-d\TH:i:s.u\Z'));

        $this->_customer->refresh();
        $debtAmountSumAfterPayment = 0;
        foreach ($this->_customer->installments as $installment) {
            $debtAmountSumAfterPayment += $installment->debtAmount;
        }
        $this->assertEquals(245000, $debtAmountSumAfterPayment);
    }

    #[Test]
    public function successForMultiInstallmentsWithFullDebtAmount(): void
    {
        Installment::factory()->count(2)->create($this->_installmentInput);

        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->postJson(
                route('uniredCallback.webHook'),
                array_merge($this->_webHookInput, ['amount' => 690000 * 100])
            );

        $response->assertOk();
        $this->assertArrayHasKey('success', $response->json());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertTrue($response->json('success'));
        $this->assertEquals('OK', $response->json('message'));

        $this->assertEquals(2, Transaction::query()->where('amount', 345000)->count());

        foreach (Transaction::query()->get() as $transaction) {
            $this->assertEquals(TransactionType::UNIRED_WITH_PINFL, $transaction->transaction_type_id);
            $this->assertEquals($this->_webHookInput['ext_id'], $transaction->external_id);
            $this->assertContains($transaction->installment_id, $this->_customer->installments->pluck('id'));
            $this->assertEquals($this->_customer->id, $transaction->customer_id);
            $this->assertEquals($this->_webHookInput['card_mask'], $transaction->card_pan);
            $this->assertEquals($this->_webHookInput['created_at'], $transaction->paid_at?->format('Y-m-d\TH:i:s.u\Z'));
        }

        $this->_customer->refresh();
        $debtAmountSumAfterPayment = 0;
        foreach ($this->_customer->installments as $installment) {
            $debtAmountSumAfterPayment += $installment->debtAmount;
        }

        $this->assertEquals(0, $debtAmountSumAfterPayment);
    }

    #[Test]
    public function successCheckPayment(): void
    {
        Installment::factory()->create(array_merge($this->_installmentInput, ['status' => InstallmentStatusEnum::INACTIVE]));
        Installment::factory()->create(array_merge($this->_installmentInput, ['starts_in' => Carbon::now()->addDay()]));
        Installment::factory()->count(2)->create($this->_installmentInput);

        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->getJson(
                route('uniredCallback.checkPayment', $this->_checkPaymentInput)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'success',
            'code',
            'message',
            'result' => [
                'debt',
            ],
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertEquals(0, $response->json('code'));
        $this->assertEquals('SUCCESS', $response->json('message'));
        $this->assertEquals(690000 * 100, $response->json('result.debt'));
    }

    #[Test]
    public function successCheckPaymentIfDebtAmountIsZero(): void
    {
        Installment::factory()->create(array_merge($this->_installmentInput, ['status' => InstallmentStatusEnum::INACTIVE]));
        Installment::factory()->create(array_merge($this->_installmentInput, ['starts_in' => Carbon::now()->addDay()]));

        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->getJson(
                route('uniredCallback.checkPayment', $this->_checkPaymentInput)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'success',
            'code',
            'message',
            'result' => [
                'debt',
            ],
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertEquals(0, $response->json('code'));
        $this->assertEquals('SUCCESS', $response->json('message'));
        $this->assertEquals(0, $response->json('result.debt'));
    }

    #[Test]
    public function failCheckPayment(): void
    {
        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->getJson(
                route(
                    'uniredCallback.checkPayment',
                    array_merge($this->_checkPaymentInput, ['loan_id' => Utilities::generateIdByTenancy(999999999)]))
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'success',
            'code',
            'message',
            'result',
        ]);
        $this->assertFalse($response->json('success'));
        $this->assertEquals(199, $response->json('code'));
        $this->assertEquals('Не правильные параметры: invalid_params : loan id invalid', $response->json('message'));
        $this->assertNull($response->json('result'));
    }

    #[Test]
    public function successSendSms(): void
    {
        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->postJson(route('uniredCallback.sendSms'), $this->_sendSmsInput);

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('OK', $response->json('message'));
    }

    #[Test]
    public function failAuthorization()
    {
        $response = $this
            ->withHeader('Authorization', 'value')
            ->postJson(route('uniredCallback.webHook'), $this->_webHookInput);

        $response->assertStatus(403);
        $this->assertArrayHasKey('error', $response->json());
        $this->assertEquals('Unauthorized', $response->json('error'));
    }

    #[Test]
    public function cannotFindCompany()
    {
        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->postJson(
                route('uniredCallback.webHook'),
                array_merge($this->_webHookInput, ['loan_id' => 999999999 . Utilities::TENANT_SEPARATOR . $this->_customer->id])
            );
        $response->assertStatus(404);
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('Not Found', $response->json('message'));
    }

    #[Test]
    public function cannotFindCustomer()
    {
        $response = $this
            ->withHeader('Authorization', $this->_token)
            ->postJson(
                route('uniredCallback.webHook'),
                array_merge($this->_webHookInput, ['loan_id' => Utilities::generateIdByTenancy(999999999)])
            );
        $response->assertStatus(404);
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('Not Found', $response->json('message'));
    }
}
