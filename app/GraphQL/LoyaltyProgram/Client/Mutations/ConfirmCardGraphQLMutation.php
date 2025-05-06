<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Client\Types\CardGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\LoyaltyProgram\Card;
use App\Services\LoyaltyProgram\LoyaltyProgramUserCardService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ConfirmCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'confirmCard',
    ];

    public function __construct(private readonly LoyaltyProgramUserCardService $userCardService) {}

    public function args(): array
    {
        return [
            'card_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Card::class, 'id')->where('user_id', auth('loyalty-program-client')->id()),
                ],
            ],
            'transaction_id' => [
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|int',
            ],
            'otp' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->userCardService->confirmCard(auth('loyalty-program-client')->id(), $args);
    }
}
