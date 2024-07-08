<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Attachment;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateDocumentGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createDocument',
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
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:64',
                ],
            ],
            'file' => [
                'name' => 'file',
                'type' => GraphQL::type('Upload'),
                'rules' => [
                    'required',
                    'file',
                    'mimes:docx,doc,pdf,png,jpg,jpeg',
                    'max:10240',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPLOAD_DOCUMENT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $installment = Installment::findOrFail($args['installment_id']);

        return Attachment::upload($args['file'], AttachmentTypeEnum::INSTALLMENT_DOCUMENT, $installment, $args['name']);
    }
}
