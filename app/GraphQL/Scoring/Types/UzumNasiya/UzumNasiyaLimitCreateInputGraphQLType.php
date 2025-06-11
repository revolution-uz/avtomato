<?php

namespace App\GraphQL\Scoring\Types\UzumNasiya;

use App\Rules\PhoneRule;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class UzumNasiyaLimitCreateInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'UzumNasiyaLimitCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'digits:12', new PhoneRule],
            ],
        ];
    }
}
