<?php

namespace App\GraphQL\Ecommerce\Common\Queries;

use App\GraphQL\Ecommerce\Admin\Types\BannerGraphQLType;
use App\Models\Company\Ecommerce\Banner;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleBannerGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleBanner',
    ];

    public function type(): Type
    {
        return GraphQL::type(BannerGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(Banner::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Banner::query()->findOrFail($args['id']);
    }
}
