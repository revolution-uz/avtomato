<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Client\Types\ProfileGraphQLType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DetachRecipientGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachRecipient',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProfileGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $distributor = auth('loyalty-program-client')->user();

        if (! $recipient = $distributor->recipient) {
            throw ValidationException::withMessages(['phone' => 'No recipient is currently attached to this distributor']);
        }

        $recipient->distributor()->disassociate();
        $recipient->saveOrFail();

        return $distributor;
    }
}
