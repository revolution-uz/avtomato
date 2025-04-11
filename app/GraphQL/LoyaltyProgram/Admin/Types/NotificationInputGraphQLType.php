<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class NotificationInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'NotificationInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'title_uz' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:255',
            ],
            'title_ru' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:255',
            ],
            'body_uz' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:1000',
            ],
            'body_ru' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:1000',
            ],
            'image_uz_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:loyalty_program_images,id,deleted_at,NULL'],
            ],
            'image_ru_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:loyalty_program_images,id,deleted_at,NULL'],
            ],
        ];
    }
}
