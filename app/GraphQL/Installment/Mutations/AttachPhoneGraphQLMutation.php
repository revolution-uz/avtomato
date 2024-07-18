<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Models\Company\Phone;
use App\Rules\PhoneRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachPhoneGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachPhone',
    ];

    public function args(): array
    {
        return [
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:installments,id'],
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => ['distinct', 'required', 'int', new PhoneRule],
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int|in:' . implode(',', PhoneTypeEnum::installmentValues()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $installment = Installment::findOrFail($args['installment_id']);
        /** @var Phone $phone */
        $phone = Phone::query()->firstOrCreate(['phone' => $args['phone']]);

        $installment->phones()->attach($phone->id, ['type' => $args['type']]);
        $installment->refresh();

        return $installment;
    }
}
