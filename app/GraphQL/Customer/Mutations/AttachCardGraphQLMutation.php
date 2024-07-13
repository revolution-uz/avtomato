<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\AttachCardGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
use App\Services\CustomerCardService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachCard',
    ];

    public function __construct(
        private readonly CustomerCardService $customerCardService,
    ) {}

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'number' => [
                'name' => 'number',
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'rules' => 'required|integer|digits:16',
            ],
            'expiry' => [
                'name' => 'expiry',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'size:4',
                    'regex:/^[0-9]{4}$/',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachCardGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        [$card, $cardServiceResponse] = $this->customerCardService->attachCard(
            $args['customer_id'],
            Arr::except($args, ['customer_id']),
        );

        return [$card, $cardServiceResponse];
    }
}
