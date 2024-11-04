<?php

namespace App\GraphQL\Common\Types;

use App\Enums\Main\SettingsEnum;
use App\GraphQL\Scalars\BigInteger;
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

    public function __construct(
    ) {}

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
                    return Auth::check() ? BalanceService::getBalance() : 0;
                },
            ],
        ];
    }
}
