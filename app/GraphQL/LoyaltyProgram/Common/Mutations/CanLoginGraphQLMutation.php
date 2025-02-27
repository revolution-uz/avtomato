<?php

namespace App\GraphQL\LoyaltyProgram\Common\Mutations;

use App\Exceptions\GeneralException;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CanLoginGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'canLogin',
    ];

    public function __construct(private readonly ConfirmationService $confirmationService) {}

    public function type(): Type
    {
        return Type::nonNull(Type::int());
    }

    public function args(): array
    {
        return [
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'digits:12', new PhoneRule],
            ],
        ];
    }

    /**
     * @throws GeneralException
     */
    public function resolve($root, $args): int
    {
        try {
            return $this->confirmationService->canLoginIntoLoyaltyProgramm($args['phone'], request()->header('Device-Name'))->value;
        } catch (Throwable $e) {
            throw new GeneralException($e->getMessage());
        }
    }
}
