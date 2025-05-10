<?php

namespace App\GraphQL\LoyaltyProgram\Common\Queries;

use App\GraphQL\LoyaltyProgram\Common\Types\OnboardingImageGraphQLType;
use App\Models\Company\LoyaltyProgram\OnboardingImage;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Query;

class OnboardingImagesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'onboardingImages',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(OnboardingImageGraphQLType::type()));
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'regex:/^[-]?[a-z_]+$/'],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return OnboardingImage::query()
            ->where('is_active', true)
            ->when(
                isset($args['sort']),
                fn (Builder $q) => $q->orderBySortable($args['sort']),
                fn (Builder $q) => $q->orderByDesc('order')
            )
            ->get();
    }
}
