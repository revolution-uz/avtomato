<?php

namespace App\GraphQL\Ecommerce\Common\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Exceptions\GeneralException;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class SendOtpGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'ecommerceRegister',
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
        $company = Company::query()->findOrFail(tenant('id'));
        if ($company->status !== CompanyStatusEnum::ACTIVE) {
            throw new GeneralException('Company is not active');
        }
        try {
            $this->confirmationService->send($args['phone'], isEcommerceOtp: true);

            return true;
        } catch (Throwable $e) {
            throw new GeneralException($e->getMessage());
        }
    }
}
