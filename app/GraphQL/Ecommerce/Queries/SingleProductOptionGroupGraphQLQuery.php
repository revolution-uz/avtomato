<?php

namespace App\GraphQL\Ecommerce\Queries;

use App\GraphQL\Ecommerce\Types\ProductOptionGroupGraphQLType;
use App\Models\Company\Ecommerce\ProductOptionGroup;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleProductOptionGroupGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleProductOptionGroup',
    ];

    public function type(): Type
    {
        return GraphQL::type(ProductOptionGroupGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductOptionGroup::class, 'id')
                        ->where(function ($query) {
                            $query->whereNull('deleted_at');
                        }),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ProductOptionGroup::query()->findOrFail($args['id']);
    }
}
