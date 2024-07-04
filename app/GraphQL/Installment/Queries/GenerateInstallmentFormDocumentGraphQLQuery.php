<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Services\DocumentGeneratorService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GenerateInstallmentFormDocumentGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'generateInstallmentFormDocument',
    ];

    public function __construct(
        private readonly DocumentGeneratorService $documentGeneratorService
    ) {}

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', Rule::exists('installments', 'id')->whereNull('deleted_at')],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPLOAD_DOCUMENT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->documentGeneratorService->findOrCreateInstallmentForm(Installment::findOrFail($args['id']));
    }
}
