<?php

namespace App\GraphQL\LoyaltyProgram\Common\Mutations;

use App\Enums\Company\LoyaltyProgramm\CanLoginTypeEnum;
use App\Exceptions\GeneralException;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class CanLoginGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'loyaltyProgramCanLogin',
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
        $deviceName = request()->header('Device-Name');
        if (empty($deviceName)) {
            return CanLoginTypeEnum::CANNOT_LOGIN->value;
        }

        return $this->confirmationService->canLoginIntoLoyaltyProgram($args['phone'], $deviceName)->value;
    }
}
