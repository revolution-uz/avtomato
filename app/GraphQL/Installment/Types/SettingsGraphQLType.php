<?php

namespace App\GraphQL\Installment\Types;

use App\Models\Company\CompanySettings;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SettingsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SettingsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CompanySettings $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CompanySettings $root) => $root->name->value,
            ],
            'label' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CompanySettings $root) => $root->name->getLabel(),
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CompanySettings $root) => $root->type->value,
            ],
            'value' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CompanySettings $root) => $root->value,
            ],
        ];
    }
}
