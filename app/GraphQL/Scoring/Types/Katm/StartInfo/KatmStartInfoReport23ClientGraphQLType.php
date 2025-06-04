<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23ClientDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23ClientGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23ClientGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'katm_sir' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->katm_sir,
            ],
            'juridical_status' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->juridical_status,
            ],
            'client_type' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->client_type,
            ],
            'name' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->name,
            ],
            'gender' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->gender,
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->pinfl,
            ],
            'birth_date' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->birth_date,
            ],
            'registration_region' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->registration_region,
            ],
            'registration_district' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->registration_district,
            ],
            'registration_address' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->registration_address,
            ],
            'registration_address_change' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->registration_address_change,
            ],
            'live_address' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->live_address,
            ],
            'live_address_change' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->live_address_change,
            ],
            'email' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->email,
            ],
            'email_change' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->email_change,
            ],
            'inn' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->inn,
            ],
            'oked' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->oked,
            ],
            'founders' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->founders,
            ],
            'phones' => [
                'type' => Type::listOf(GraphQL::type(KatmStartInfoReport23ClientPhoneGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (KatmStartInfoReport23ClientDTO $root) => $root->phones,
            ],
        ];
    }
}
