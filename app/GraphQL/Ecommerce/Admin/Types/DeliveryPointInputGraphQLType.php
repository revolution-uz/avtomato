<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class DeliveryPointInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'DeliveryPointInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'name_uz' => [
                'name' => 'name_uz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'name_ru' => [
                'name' => 'name_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'name_en' => [
                'name' => 'name_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'name_oz' => [
                'name' => 'name_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'description_uz' => [
                'name' => 'description_uz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'description_ru' => [
                'name' => 'description_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'description_en' => [
                'name' => 'description_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'description_oz' => [
                'name' => 'description_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'is_prepaid' => [
                'name' => 'is_prepaid',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => 'required|boolean',
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|regex:/^\+998\d{9}$/',
            ],
            'address_uz' => [
                'name' => 'address_uz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'address_ru' => [
                'name' => 'address_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'address_en' => [
                'name' => 'address_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'address_oz' => [
                'name' => 'address_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'landmark_uz' => [
                'name' => 'landmark_uz',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'landmark_ru' => [
                'name' => 'landmark_ru',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'landmark_en' => [
                'name' => 'landmark_en',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'landmark_oz' => [
                'name' => 'landmark_oz',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'location_url' => [
                'name' => 'location_url',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'lat' => [
                'name' => 'lat',
                'type' => Type::float(),
                'rules' => 'nullable|numeric',
            ],
            'lng' => [
                'name' => 'lng',
                'type' => Type::float(),
                'rules' => 'nullable|numeric',
            ],
            'region_id' => [
                'name' => 'region_id',
                'type' => Type::int(),
                'rules' => 'nullable|int|exists:regions,id',
            ],
            'district_id' => [
                'name' => 'district_id',
                'type' => Type::int(),
                'rules' => 'nullable|int|exists:districts,id',
            ],
            'mahalla_id' => [
                'name' => 'mahalla_id',
                'type' => Type::int(),
                'rules' => 'nullable|int|exists:mahallas,id',
            ],
        ];
    }
}
