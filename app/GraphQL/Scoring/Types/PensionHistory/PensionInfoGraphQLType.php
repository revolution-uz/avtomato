<?php

namespace App\GraphQL\Scoring\Types\PensionHistory;

use App\GraphQL\Scoring\Types\KatmCreateRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PensionInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PensionInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Pension Info GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->id;
                },
            ],
            'ext_id' => [
                'type' => Type::int(),
                'description' => 'External ID of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->ext_id;
                },
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Status of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->status;
                },
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Type of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->type;
                },
            ],
            'client_code' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Client code of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->client_code;
                },
            ],
            'customer_id' => [
                'type' => Type::int(),
                'description' => 'Customer ID of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->customer_id;
                },
            ],
            'card_id' => [
                'type' => Type::int(),
                'description' => 'Card ID of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->card_id;
                },
            ],
            'request' => [
                'type' => GraphQL::type(KatmCreateRequestGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Request of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->request;
                },
            ],
            'response' => [
                'type' => GraphQL::type(KatmPensionInfoResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Request of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->response;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Scoring request created timestamp',
                'resolve' => function (ScoringRequest $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Scoring request updated timestamp',
                'resolve' => function (ScoringRequest $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'Scoring request deleted timestamp',
                'resolve' => function (ScoringRequest $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
