<?php

namespace App\GraphQL\Customer\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CustomerByPassportGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'customerByPassport',
    ];

    public function __construct()
    {
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::string(),
            ],
            'passport_series' => [
                'name' => 'passport_series',
                'type' => Type::string(),
            ],
            'passport_number' => [
                'name' => 'passport_number',
                'type' => Type::string(),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_INFO_BY_PASSPORT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Customer::query()
            ->when($args['pinfl'], fn (Builder $q) => $q->where('pinfl', $args['pinfl']))
            ->when(
                $args['passport_series'] && $args['passport_number'],
                fn (Builder $q) => $q
                    ->where('passport_series', $args['passport_series'])
                    ->where('passport_number', $args['passport_number'])
            )->firstOrFail();
    }

    protected function rules(array $args = []): array
    {
        return [
            'pinfl' => [
                'nullable',
                'string',
                'size:14',
                'regex:/^[0-9]+$/',
                Rule::requiredIf(empty($args['passport_series']) && empty($args['passport_number'])),
            ],
            'passport_series' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Z]+$/',
                Rule::requiredIf(empty($args['pinfl'])),
            ],
            'passport_number' => [
                'nullable',
                'string',
                'size:7',
                'regex:/^[0-9]+$/',
                Rule::requiredIf(empty($args['pinfl'])),
            ],
        ];
    }
}
