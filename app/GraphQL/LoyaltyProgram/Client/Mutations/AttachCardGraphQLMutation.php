<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Client\Types\AttachCardGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Services\LoyaltyProgram\LoyaltyProgramUserCardService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachCard',
    ];

    public function __construct(private readonly LoyaltyProgramUserCardService $userCardService) {}

    public function args(): array
    {
        return [
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|integer|digits:16',
            ],
            'expiry' => [
                'name' => 'expiry',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|digits:4|date_format:ym',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachCardGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        [$card, $cardServiceResponse] = $this->userCardService->attachCard(auth('loyalty-program-client')->id(), $args);

        return [$card, $cardServiceResponse];
    }
}
