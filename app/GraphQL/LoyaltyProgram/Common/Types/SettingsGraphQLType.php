<?php

namespace App\GraphQL\LoyaltyProgram\Common\Types;

use App\Enums\Company\CompanySettingsEnum;
use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\CompanySettings;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SettingsGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const GRAPHQL_TYPE_NAME = 'SettingsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'app_maintenance_mode' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Collection $root) => $root
                    ->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::LOYALTY_PROGRAM_APP_MAINTENANCE_MODE)
                    ?->value ?: true,
            ],
            'support_bot_username' => [
                'type' => Type::string(),
                'resolve' => fn (Collection $root) => $root
                    ->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::LOYALTY_PROGRAM_SUPPORT_BOT_USERNAME)
                    ?->value ?: null,
            ],
            'support_phone' => [
                'type' => Type::string(),
                'resolve' => fn (Collection $root) => $root
                    ->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::LOYALTY_PROGRAM_SUPPORT_PHONE)
                    ?->value ?: null,
            ],
            'app_min_version' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Collection $root) => $root
                    ->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::LOYALTY_PROGRAM_APP_MIN_VERSION)
                    ?->value ?: 1,
            ],
            'update_type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Collection $root) => $root
                    ->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::LOYALTY_PROGRAM_UPDATE_TYPE)
                    ?->value ?: 1,
            ],
        ];
    }
}
