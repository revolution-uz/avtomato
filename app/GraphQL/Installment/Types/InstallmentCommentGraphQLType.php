<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Management\Types\AdminGraphQLType;
use App\Models\Company\InstallmentComment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentCommentGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentCommentGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentComment $root) => $root->id,
            ],
            'installment_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentComment $root) => $root->installment_id,
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (InstallmentComment $root) => $root->installment_id,
            ],
            'admin_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentComment $root) => $root->admin_id,
            ],
            'admin' => [
                'type' => Type::nonNull(GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (InstallmentComment $root) => $root->admin,
            ],
            'comment' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (InstallmentComment $root) => $root->comment,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentComment $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentComment $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentComment $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
