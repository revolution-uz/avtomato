<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Services\Installment\InstallmentService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ChangeStatusGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeStatus',
    ];

    public function __construct(
        protected InstallmentService $installmentService
    ) {}

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_EDIT);

        return [
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id')->whereNull('deleted_at')->when($branches, fn ($q) => $q->whereIn('branch_id', $branches)),
                ],
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::int()),
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
        if ($this->installmentService->changeStatus($installment, InstallmentStatusEnum::tryFrom($args['status']))) {
            $installment->refresh();

            return $installment;
        }
        throw new AccessDeniedException('You cannot start this installment');
    }

    protected function rules(array $args = []): array
    {
        return [
            'status' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($args) {
                    $installment = Installment::find($args['installment_id']);
                    $status = InstallmentStatusEnum::tryFrom($value);

                    if (! $status) {
                        $fail('Status is not valid');
                    }
                    if ($installment && ! in_array($installment->status, InstallmentStatusEnum::getStatusListByStatus($status))) {
                        $fail('Status is not valid for this installment');
                    }
                },
            ],
        ];
    }
}
