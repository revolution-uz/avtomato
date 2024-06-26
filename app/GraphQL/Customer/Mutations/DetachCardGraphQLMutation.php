<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Card;
use App\Models\Company\InstallmentCard;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DetachCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachCard',
    ];

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int()))),
                'rules' => ['required', 'array', 'distinct', function (string $attribute, mixed $value, Closure $fail) {
                    foreach ($value as $cardId) {
                        if (! Card::query()->where('id', $cardId)->exists()) {
                            $fail(__('api.not found'));
                        }
                        if (InstallmentCard::query()->where('card_id', $cardId)->exists()) {
                            $fail(__('api.are used', [
                                'item' => __('api.Card'),
                                'element' => __('api.Installment'),
                            ]));
                        }
                    }
                }, ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        Card::query()
            ->whereIn('id', $args['ids'])
            ->where('customer_id', $args['customer_id'])
            ->delete();

        return true;
    }
}
