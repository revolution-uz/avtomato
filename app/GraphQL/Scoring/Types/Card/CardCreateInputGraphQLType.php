<?php

namespace App\GraphQL\Scoring\Types\Card;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scalars\BigInteger;
use App\Rules\PhoneRule;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class CardCreateInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'CardCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'number' => [
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'rules' => 'required|integer|digits:16',
            ],
            'expiry' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|digits:4|date_format:ym',
            ],
            'phone' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'required_if:type,' . ScoringRequestTypeEnum::HUMO->value, 'digits:12', new PhoneRule],
            ],
        ];
    }
}
