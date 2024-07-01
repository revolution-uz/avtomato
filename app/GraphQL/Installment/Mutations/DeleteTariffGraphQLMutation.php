<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteTariffGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTariff',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TARIFF_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        Tariff::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (! Tariff::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if (Installment::query()->where('tariff_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Tariff'),
                            'element' => __('api.Installment'),
                        ]));
                    }
                },
            ],
        ];
    }
}
