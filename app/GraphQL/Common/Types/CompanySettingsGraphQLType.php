<?php

namespace App\GraphQL\Common\Types;

use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CompanySettingsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CompanySettingsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Company Settings GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'card_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
            'pinfl_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::PINFL_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
            'guarantor_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
            'guarantor_pinfl_after_due_days' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Card After Due Days',
                'resolve' => function (Collection $root) {
                    /** @var Collection<int, CompanySettings> $root */
                    return $root->first(fn (CompanySettings $settings) => $settings->name === CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS)->value ?? 0;
                },
            ],
        ];
    }
}
