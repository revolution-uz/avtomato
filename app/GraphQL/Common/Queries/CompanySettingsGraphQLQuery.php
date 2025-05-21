<?php

namespace App\GraphQL\Common\Queries;

use App\GraphQL\Common\Types\CompanySettingsGraphQLType;
use App\Models\Company\CompanySettings;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CompanySettingsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'companySettings',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CompanySettingsGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CompanySettings::query()->get();
    }
}
