<?php

namespace App\GraphQL\User\Types;

use App\Models\Company\ActivityLog;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ActivityLogGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'ActivityLogGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => ActivityLog::class, // to get sortable columns
    ];

    public static function logMessageBuilder($modelName, $modelId, $event): string
    {
        $modelName = trans('models.' . $modelName);

        return trans('logging.' . $event, ['entity' => $modelName, 'id' => $modelId]);
    }

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (ActivityLog $root) {
                    return $root->id;
                },
            ],
            'admin_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (ActivityLog $root) {
                    return $root->causer_id;
                },
            ],
            'admin' => [
                'type' => Type::nonNull(GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => function (ActivityLog $root) {
                    return $root->causer;
                },
            ],
            // TODO write format for
            //            'properties' => [
            //                'type' => Type::nonNull(GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME)),
            //                'resolve' => function (ActivityLog $root) {
            //                    return $root->causer;
            //                },
            //            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (ActivityLog $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (ActivityLog $root) {
                    return self::logMessageBuilder($root->subject_type, $root->subject_id, $root->event->value);
                },
            ],
        ];
    }
}
