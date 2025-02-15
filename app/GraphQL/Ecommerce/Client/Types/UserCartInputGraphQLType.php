<?php

namespace App\GraphQL\Ecommerce\Client\Types;

use App\Models\Company\Ecommerce\ProductVariation;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class UserCartInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'UserCartInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'variation_id' => [
                'name' => 'variation_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductVariation::class, 'id')->where('is_active', true)->whereNull('deleted_at'),
                ],
            ],
            'quantity' => [
                'name' => 'quantity',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|integer|min:1',
            ],
        ];
    }
}
