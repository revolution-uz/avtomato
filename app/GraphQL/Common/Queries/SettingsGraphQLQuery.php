<?php

namespace App\GraphQL\Common\Queries;

use App\GraphQL\Common\Types\SettingsGraphQLType;
use App\Models\Company\CompanySettings;
use App\Models\Settings;
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
        return Type::nonNull(GraphQL::type(SettingsGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return array_merge(Settings::query()->get(), CompanySettings::query()->get());
    }
}
