<?php

namespace App\GraphQL\Scoring\Queries;

use App\Clients\RoyxatClient;
use App\DTOs\Royxat\RoyxatCreditResponseDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Scoring\Types\Partners\RoyxatCreditGraphQLType;
use App\Helpers\RbacHelper;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class RoyxatCreditsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'royxatCredits',
    ];

    private RoyxatClient $royxatClient;

    public function __construct(RoyxatClient $royxatClient)
    {
        $this->royxatClient = $royxatClient;
    }

    public function type(): Type
    {
        return Type::listOf(GraphQL::type(RoyxatCreditGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'passport' => [
                'type' => Type::string(),
                'rules' => 'required_without_all:pinfl,tin|nullable|string|max:9|min:9',
            ],
            'pinfl' => [
                'type' => Type::string(),
                'rules' => 'required_without_all:passport,tin|nullable|integer|digits:14',
            ],
            'tin' => [
                'type' => Type::string(),
                'rules' => 'required_without_all:passport,pinfl|nullable|integer|digits:9',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_ROYXAT_CREDIT_HISTORY_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $attribute = $args['passport'] ?? null;
        $attributeName = 'passport_id';

        if (isset($args['pinfl'])) {
            $attribute = $args['pinfl'];
            $attributeName = 'pinfl';
        }

        if (isset($args['tin'])) {
            $attribute = $args['tin'];
            $attributeName = 'tin';
        }

        return RoyxatCreditResponseDTO::collect($this->royxatClient->getCredits($attribute, $attributeName));
    }
}
