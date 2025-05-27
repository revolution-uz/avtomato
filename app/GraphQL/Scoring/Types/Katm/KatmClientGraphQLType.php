<?php

namespace App\GraphQL\Scoring\Types\Katm;

use App\DTOs\Scoring\Katm\KatmClientDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmClientGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmClientGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Client GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'name',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->name;
                },
            ],
            'birth_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'birth date',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->birth_date;
                },
            ],
            'gender' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'gender',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->gender;
                },
            ],
            'address' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'address',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->address;
                },
            ],
            'phone_number' => [
                'type' => Type::string(),
                'description' => 'phone number',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->phone_number;
                },
            ],
            'resident' => [
                'type' => Type::string(),
                'description' => 'resident',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->resident;
                },
            ],
            'pinfl' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'pinfl',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->pinfl;
                },
            ],
            'inn' => [
                'type' => Type::string(),
                'description' => 'inn',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->inn;
                },
            ],
            'document_type' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'document_type',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->document_type;
                },
            ],
            'document_serial' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'document_serial',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->document_serial;
                },
            ],
            'document_number' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'document_number',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->document_number;
                },
            ],
            'document_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'document_date',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->document_date;
                },
            ],
            'document_type_id' => [
                'type' => Type::string(),
                'description' => 'document_type_id',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->document_type_id;
                },
            ],
            'subject_id' => [
                'type' => Type::string(),
                'description' => 'subject_id',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->subject_id;
                },
            ],
            'credit_start_date' => [
                'type' => Type::string(),
                'description' => 'credit_start_date',
                'resolve' => function (KatmClientDTO $root) {
                    return $root->credit_start_date;
                },
            ],
        ];
    }
}
