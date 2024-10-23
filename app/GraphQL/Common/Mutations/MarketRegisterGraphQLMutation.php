<?php

namespace App\GraphQL\Common\Mutations;

use App\Enums\Company\AllowedEnum;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\CustomerTypeEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Exceptions\GeneralException;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company;
use App\Models\Company\Phone;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use App\Services\CustomerService;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class MarketRegisterGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'marketRegister',
    ];

    public function __construct(
        private readonly ConfirmationService $confirmationService,
    ) {}

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => ['distinct', 'required', 'int', new PhoneRule],
            ],
        ];
    }

    /**
     * @throws GeneralException
     */
    public function resolve($root, $args): bool
    {
        $company = Company::findOrFail(tenant('id'));
        if ($company->status !== CompanyStatusEnum::ACTIVE) {
            throw new GeneralException('Company is not active');
        }
        try {
            if ($args['phone'] === AllowedEnum::PHONE->value) {
                return true;
            }
            $customerService = new CustomerService;
            $customer = $customerService->saveCustomer(['type' => CustomerTypeEnum::MARKET], null, CustomerTypeEnum::MARKET);
            $phone = Phone::query()->firstOrCreate(['phone' => $args['phone']]);

            $customer->phones()->attach($phone->id, ['type' => PhoneTypeEnum::PRIVATE, 'comment' => 'Market']);
            $this->confirmationService->send($args['phone'], isMarketOtp: true);

            return true;
        } catch (Throwable $e) {
            throw new GeneralException($e->getMessage());
        }
    }
}
