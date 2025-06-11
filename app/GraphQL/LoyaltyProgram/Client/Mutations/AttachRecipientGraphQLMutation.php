<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Client\Types\ProfileGraphQLType;
use App\Models\Company\LoyaltyProgram\User;
use App\Rules\PhoneRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachRecipientGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachRecipient',
    ];

    public function args(): array
    {
        return [
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'digits:12',
                    new PhoneRule,
                    Rule::exists(User::class, 'phone')
                        ->whereNot('id', auth('loyalty-program-client')->id())
                        ->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProfileGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $distributor = auth('loyalty-program-client')->user();

        if ($distributor->recipient) {
            throw ValidationException::withMessages(['phone' => 'Recipient already attached']);
        }

        if ($distributor->distributed_at && $distributor->distributed_at >= Carbon::now()->subMonth()) {
            throw ValidationException::withMessages(['phone' => 'You cannot attach a recipient within the same month']);
        }

        $recipient = User::where('phone', $args['phone'])->firstOrFail();
        $recipient->distributor()->associate($distributor);
        $distributor->touch('distributed_at');
        $recipient->saveOrFail();

        return $distributor;
    }
}
