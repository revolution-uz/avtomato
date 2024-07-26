<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class AttachCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachCard',
    ];

    public function args(): array
    {
        return [
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id')->whereNull('deleted_at'),
                ],
            ],
            'card_id' => [
                'name' => 'card_id',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_ATTACH_CARD);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $installment = Installment::findOrFail($args['installment_id']);
        $installment->cards()->attach($args['card_id']);

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'card_id' => [
                'required',
                'integer',
                Rule::exists('cards', 'id')->whereNull('deleted_at')->where('status', CardStatusEnum::ACTIVE),
                Rule::unique('installment_cards', 'card_id')
                    ->where('installment_id', $args['installment_id'])->whereNull('deleted_at'),
            ],
        ];
    }
}



