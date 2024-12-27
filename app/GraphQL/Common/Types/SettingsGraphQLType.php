<?php

namespace App\GraphQL\Common\Types;

use App\Enums\Main\SettingsEnum;
use App\GraphQL\GraphQLTypeHelper;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\Module;
use App\Models\Settings;
use App\Services\User\BalanceService;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, Settings> $root */
                    return $root->first(fn (Settings $settings) => $settings->name === SettingsEnum::PARTNER_APP_MAINTENANCE_MODE)->value ?? true;
                },
            ],
            'web_maintenance_mode' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, Settings> $root */
                    return $root->first(fn (Settings $settings) => $settings->name === SettingsEnum::PARTNER_WEB_MAINTENANCE_MODE)->value ?? true;
                },
            ],
            'app_min_version' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, Settings> $root */
                    return $root->first(fn (Settings $settings) => $settings->name === SettingsEnum::PARTNER_APP_MIN_VERSION)->value ?? 0;
                },
            ],
            'balance' => [
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'resolve' => function ($root) {
                    return auth()->check() ? BalanceService::getBalance() : 0;
                },
            ],
            'multibranch_module' => [
                'type' => Type::string(),
                'resolve' => fn ($root) => auth()->check() ? Module::getMultiBranchModule()?->name->value : null,
            ],
        ];
    }
}
