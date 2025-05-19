<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class ProductOptionValueInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'ProductOptionValueInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'value_uz' => [
                'name' => 'value_uz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'value_ru' => [
                'name' => 'value_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'value_en' => [
                'name' => 'value_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if (ProductOptionValue::query()->where('slug', Str::slug($value))->exists()) {
                            $fail(trans('validation.unique', ['attribute' => 'value']));
                        }
                    },
                ],
            ],
            'value_oz' => [
                'name' => 'value_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'option_id' => [
                'name' => 'option_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(ProductOption::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'image_id' => [
                'name' => 'image_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::exists(EcommerceImage::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }
}
