<?php

namespace App\GraphQL\Marketing\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\SmsTemplate;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SmsTemplateGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'SmsTemplateGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => SmsTemplate::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SmsTemplate $root) => $root->id,
            ],
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (SmsTemplate $root) => $root->description,
            ],
            'example' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (SmsTemplate $root) => $root->getExampleMessage(),
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SmsTemplate $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SmsTemplate $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
