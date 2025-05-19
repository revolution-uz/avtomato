<?php

namespace App\GraphQL\LoyaltyProgram\Common\Queries;

use App\GraphQL\LoyaltyProgram\Common\Types\SettingsGraphQLType;
use App\Models\Company\CompanySettings;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class SettingsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'settings',
    ];

    public function type(): Type
    {
        return Type::nonNull(SettingsGraphQLType::type());
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CompanySettings::query()->get();
    }
}
