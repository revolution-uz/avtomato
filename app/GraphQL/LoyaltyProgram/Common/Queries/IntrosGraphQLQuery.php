<?php

namespace App\GraphQL\LoyaltyProgram\Common\Queries;

use App\GraphQL\LoyaltyProgram\Common\Types\IntroGraphQLType;
use App\Models\Company\LoyaltyProgram\Intro;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Query;

class IntrosGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'intros',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(IntroGraphQLType::type()));
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
        return Intro::query()
            ->where('is_active', true)
            ->when(
                isset($args['sort']),
                fn (Builder $q) => $q->orderBySortable($args['sort']),
                fn (Builder $q) => $q->orderByDesc('order')
            )
            ->get();
    }
}
