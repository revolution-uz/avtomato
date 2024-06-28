<?php

namespace App\GraphQL\Customer\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\PassportDataGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Scoring\PassportInfoService;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class InfoByPassportGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'infoByPassport',
    ];

    public function __construct(
        private readonly PassportInfoService $passportInfoService
    ) {}

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(PassportDataGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'passport_series' => [
                'name' => 'passport_series',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'size:2',
                    'regex:/^[A-Z]+$/',
                ],
            ],
            'passport_number' => [
                'name' => 'passport_number',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'size:7',
                    'regex:/^[0-9]+$/',
                ],
            ],
            'birth_date' => [
                'name' => 'birth_date',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'date',
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_INFO_BY_PASSPORT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->passportInfoService->getPassportData(
            passportSeries: $args['passport_series'],
            passportNumber: $args['passport_number'],
            birthData: Carbon::parse($args['birth_date'])
        );
    }
}
