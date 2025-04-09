<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\Enums\Company\CardStatusEnum;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class CardInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'CardInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', Rule::enum(CardStatusEnum::class)],
            ],
        ];
    }
}
