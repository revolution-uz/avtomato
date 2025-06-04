<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23ClientPhoneDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23ClientPhoneGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23ClientPhoneGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phone_number' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientPhoneDTO $root) => $root->phone_number,
            ],
            'phone_number_change' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientPhoneDTO $root) => $root->phone_number_change,
            ],
        ];
    }
}
