<?php

namespace App\GraphQL\Tariff\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Tariff\Types\TariffGraphQLType;
use App\GraphQL\Tariff\Types\TariffInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Tariff;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateTariffGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTariff',
    ];

    public function args(): array
    {
        return [
            'tariff' => [
                'name' => 'tariff',
                'type' => Type::nonNull(GraphQL::type(TariffInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TariffGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TARIFF_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $model = new Tariff;
        $model->fill($args['tariff']);
        $model->saveOrFail();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
