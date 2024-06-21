<?php

namespace App\GraphQL\ActivityLog\Types;

use App\Models\Company\ActivityLog;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ActivityLogGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'ActivityLogGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'ActivityLog GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->id;
                },
            ],
            'event' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Event of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->event->getLabel();
                },
            ],
            'log_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Log name of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->log_name;
                },
            ],
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Description of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->description;
                },
            ],
            'subject_type' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Subject type of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->subject_type;
                },
            ],
            'subject_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Subject id of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->subject_id;
                },
            ],
            'causer_type' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Causer type of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->causer_type;
                },
            ],
            'causer_id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Causer id of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->causer_id;
                },
            ],
            'batch_uuid' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Batch uuid of ActivityLog',
                'resolve' => function (ActivityLog $root) {
                    return $root->batch_uuid;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ActivityLog created timestamp',
                'resolve' => function (ActivityLog $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ActivityLog updated timestamp',
                'resolve' => function (ActivityLog $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'ActivityLog deleted timestamp',
                'resolve' => function (ActivityLog $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
