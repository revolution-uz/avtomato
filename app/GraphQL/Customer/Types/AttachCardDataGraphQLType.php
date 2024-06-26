<?php

namespace App\GraphQL\Customer\Types;

use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttachCardDataGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'AttachCardDataGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'transaction_id' => [
                'type' => Type::int(),
                'resolve' => fn (AtmosAttachCardResponseDTO $root) => $root->transaction_id,
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => fn (AtmosAttachCardResponseDTO $root) => $root->phone,
            ],
            'result_code' => [
                'type' => Type::string(),
                'resolve' => fn (AtmosAttachCardResponseDTO $root) => $root->result->code,
            ],
            'result_description' => [
                'type' => Type::string(),
                'resolve' => fn (AtmosAttachCardResponseDTO $root) => $root->result->description,
            ],
        ];
    }
}
