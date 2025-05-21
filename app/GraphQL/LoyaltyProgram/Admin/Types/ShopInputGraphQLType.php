<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\Enums\Company\LoyaltyProgram\ShopStatusEnum;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class ShopInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ShopInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'address' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'lat' => [
                'type' => Type::float(),
                'rules' => ['nullable', 'required_with:lng', 'numeric'],
            ],
            'lng' => [
                'type' => Type::float(),
                'rules' => ['nullable', 'required_with:lat', 'numeric'],
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', Rule::enum(ShopStatusEnum::class)],
            ],
            'image_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:loyalty_program_images,id,deleted_at,NULL'],
            ],
        ];
    }
}
