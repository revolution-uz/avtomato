<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentCommentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentComment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateInstallmentCommentGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateInstallmentComment',
    ];

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_UPDATE_COMMENT);

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
            'comment_id' => [
                'name' => 'comment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installment_comments', 'id')->whereNull('deleted_at'),
                ],
            ],
            'comment' => [
                'name' => 'comment',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(InstallmentCommentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPDATE_COMMENT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $installmentComment = InstallmentComment::query()
            ->where('installment_id', $args['installment_id'])
            ->where('id', $args['comment_id'])
            ->firstOrFail();
        $installmentComment->updateOrFail($args);
        $installmentComment->refresh();

        return $installmentComment;
    }
}
