<?php

namespace App\GraphQL\EcommerceClient\Common\Queries;

use App\GraphQL\Ecommerce\Types\BannerGraphQLType;
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
                    Rule::exists('e_commerce_banners', 'id')
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
        return Banner::query()->findOrFail($args['id']);
    }
}
