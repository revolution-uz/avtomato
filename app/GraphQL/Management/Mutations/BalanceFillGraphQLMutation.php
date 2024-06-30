<?php

namespace App\GraphQL\Management\Mutations;

use App\Clients\AtmosCardClient;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\AttachCardDataGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\AtmosService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class BalanceFillGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'balanceFill',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachCardDataGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'size:16',
                    'regex:/^[0-9]{16}$/',
                ],
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

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BALANCE_FUND_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return (new AtmosService(new AtmosCardClient))->attachCard($args['number'], $args['expiry']);
    }
}
