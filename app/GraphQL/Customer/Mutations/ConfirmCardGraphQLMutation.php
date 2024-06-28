<?php

namespace App\GraphQL\Customer\Mutations;

use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\CustomerCardService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ConfirmCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'confirmCard',
    ];

    public function __construct(
        private readonly CustomerCardService $customerCardService,
    ) {}

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
            ],
            'card_id' => [
                'name' => 'card_id',
                'type' => Type::nonNull(Type::int()),
            ],
            'transaction_id' => [
                'name' => 'transaction_id',
                'type' => Type::int(),
            ],
            'otp' => [
                'name' => 'otp',
                'type' => Type::int(),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var AtmosConfirmCardResponseDTO $cardServiceResponse */
        [$card, $cardServiceResponse] = $this->customerCardService->confirmCard($args['customer_id'], Arr::except($args, ['customer_id']));

        if ($cardServiceResponse->result->isSucces()) {
            return $card;
        }
        throw ValidationException::withMessages(['otp' => $cardServiceResponse->result->description]);
    }

    protected function rules(array $args = []): array
    {
        return [
            'card_id' => [
                'required',
                'int',
                Rule::exists('cards', 'id')
                    ->where('customer_id', $args['id']),
            ],
            'transaction_id' => [
                'nullable',
                'int',
            ],
            'otp' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($args) {

                    if (isset($args['transaction_id'])) {
                        return;
                    }

                    //todo:  This is a fake OTP, it should be validated against the real OTP
                    if ($value !== '123456') {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                },
            ],
        ];
    }
}
