<?php

namespace App\GraphQL\LoyaltyProgram\Common\Mutations;

use App\Enums\Company\SmsTemplateEnum;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class SendOtpGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'loyaltyProgramSendOtp',
    ];

    public function __construct(private readonly ConfirmationService $confirmationService) {}

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'digits:12', new PhoneRule],
            ],
        ];
    }

    public function resolve($root, $args): bool
    {
        $deviceName = request()->header('Device-Name');
        if (empty($deviceName) || ! $this->confirmationService->canLoginIntoLoyaltyProgram($args['phone'], $deviceName)->isValid()) {
            throw new Error('You cannot login');
        }

        return $this->confirmationService->send($args['phone'], SmsTemplateEnum::LOYALTY_PROGRAM_OTP);

    }
}
