<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\CompanySettingsEnum;
use App\GraphQL\Installment\Types\SettingsGraphQLType;
use App\Models\Company\CompanySettings;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SettingsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'settings',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(SettingsGraphQLType::GRAPHQL_TYPE_NAME))));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CompanySettings::query()->whereIn('name', CompanySettingsEnum::installmentSettings())->get();
    }
}
