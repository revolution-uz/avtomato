<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\ProductOption;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\InputType;

class ProductOptionInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'ProductOptionInputGraphQLType';

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
                'rules' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if (ProductOption::query()->where('slug', Str::slug($value))->exists()) {
                            $fail(trans('validation.unique', ['attribute' => 'name']));
                        }
                    },
                ],
            ],
            'name_oz' => [
                'name' => 'name_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'categories' => [
                'name' => 'categories',
                'type' => Type::listOf(Type::int()),
                'rules' => 'nullable|array',
            ],
        ];
    }
}
