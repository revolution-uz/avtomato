<?php

namespace App\GraphQL\Scoring\Types\PensionHistory;

use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionDataDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmPensionDataGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmPensionDataGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Pension Data GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'namelatin' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'namelatin',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->namelatin;
                },
            ],
            'address' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'address',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->address;
                },
            ],
            'summa' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'summa',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->summa;
                },
            ],
            'patronymlatin' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'patronymlatin',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->patronymlatin;
                },
            ],
            'document' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'document',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->document;
                },
            ],
            'apptype' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'apptype',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->apptype;
                },
            ],
            'surnamelatin' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'surnamelatin',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->surnamelatin;
                },
            ],
            'experience' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'experience',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->experience;
                },
            ],
            'type' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'type',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->type;
                },
            ],
            'datepay' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'datepay',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->datepay;
                },
            ],
            'pinpp' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'pinpp',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->pinpp;
                },
            ],
            'datepens' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'datepens',
                'resolve' => function (KatmPensionDataDTO $root) {
                    return $root->datepens;
                },
            ],
        ];
    }
}
