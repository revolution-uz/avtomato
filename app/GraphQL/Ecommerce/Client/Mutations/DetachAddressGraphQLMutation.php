<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Address;
use App\Models\Company\Ecommerce\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class DetachAddressGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachAddress',
    ];

    public function args(): array
    {
        return [
            'address_id' => [
                'name' => 'address_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::exists(Address::class, 'id')
                        ->where('addressable_type', User::class)
                        ->where('addressable_id', auth('ecommerce-client')->id())
                        ->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var User $user */
        $user = auth('ecommerce-client')->user();

        return $user->addresses()->where('id', $args['address_id'])->delete();
    }
}
