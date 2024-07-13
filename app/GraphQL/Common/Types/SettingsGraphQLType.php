<?php

namespace App\GraphQL\Common\Types;

use App\Enums\Main\SettingsEnum;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\CompanySettings;
use App\Models\Settings;
use App\Services\User\BalanceService;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SettingsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'SettingsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Settings GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'app_maintenance_mode' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Mobile App Maintenance Mode',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, Settings> $root */
                    return $root->first(fn (Settings $settings) => $settings->name === SettingsEnum::PARTNER_APP_MAINTENANCE_MODE)->value ?? true;
                },
            ],
            'web_maintenance_mode' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Web Panel Maintenance Mode',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, Settings> $root */
                    return $root->first(fn (Settings $settings) => $settings->name === SettingsEnum::PARTNER_WEB_MAINTENANCE_MODE)->value ?? true;
                },
            ],
            'app_min_version' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Mobile App Minimum Version',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, Settings> $root */
                    return $root->first(fn (Settings $settings) => $settings->name === SettingsEnum::PARTNER_APP_MIN_VERSION)->value ?? 0;
                },
            ],
            'balance' => [
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'description' => 'Company balance',
                'resolve' => function ($root) {
                    return Auth::check() ? (new BalanceService)->getBalance() : 0;
                },
            ],
            'card_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === SettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
            'pinfl_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === SettingsEnum::PINFL_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
            'guarantor_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === SettingsEnum::GUARANTOR_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
            'guarantor_pinfl_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === SettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
        ];
    }
}
