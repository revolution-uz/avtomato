<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use App\Models\Company\Phoneable;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DetachPhoneGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachPhone',
    ];

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'phones' => [
                'name' => 'phones',
                'type' => Type::nonNull(Type::listOf(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME))),
                'rules' => ['required', 'array', 'distinct'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $customer = Customer::findOrFail($args['customer_id']);
        foreach ($args['phones'] as $phone) {
            /** @var Phone $phone */
            $phone = Phone::query()->where('phone', $phone)->firstOrFail();
            $customer->phones()->detach($phone->id);
        }

        return true;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'phones.*' => [
                'required',
                'int',
                Rule::exists('phones', 'phone')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) use ($args) {
                    $phone = Phone::query()->where('phone', $value)->select('id')->first();
                    if (
                        $phone && Phoneable::where('phoneable_type', Customer::class)
                            ->where('phone_id', $phone->id)
                            ->where('phoneable_id', $args['customer_id'])
                            ->doesntExist()
                    ) {
                        $fail('The selected phone does not exist.');
                    }
                },
            ],
        ]);
    }
}
