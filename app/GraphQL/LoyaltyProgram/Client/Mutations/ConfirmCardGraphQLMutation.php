<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\Clients\AtmosCardClient;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\Enums\Company\CardStatusEnum;
use App\Exceptions\GeneralException;
use App\GraphQL\LoyaltyProgram\Client\Types\CardGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\LoyaltyProgram\Card;
use App\Services\LoyaltyProgram\LoyaltyProgramUserCardService;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
                'name' => 'card_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Card::class, 'id')->where('user_id', auth('loyalty-program-client')->id()),
                ],
            ],
            'transaction_id' => [
                'name' => 'transaction_id',
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'rules' => 'required|int',
            ],
            'otp' => [
                'name' => 'otp',
                'type' => Type::string(),
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
        /** @var AtmosConfirmCardResponseDTO $cardServiceResponse */
        [$card, $cardServiceResponse] = $this->userCardService->confirmCard(auth('loyalty-program-client')->id(), $args);

        if (! $cardServiceResponse->result->isSuccess()) {
            throw ValidationException::withMessages(['otp' => $cardServiceResponse->result->description]);
        }

        try {
            $atmosCardInfoResponse = (new AtmosCardClient)->getCardInfo($card->external_id);
            if (! $atmosCardInfoResponse->result->isSuccess()) {
                throw new GeneralException('Unable to retrieve card balance');
            }
            $card_balance = $atmosCardInfoResponse->card->balance_sum / 100;
        } catch (GeneralException $e) {
            throw new Error('Unable to retrieve card balance');
        }

        if ($card_balance < 200000) {
            $card->status = CardStatusEnum::INACTIVE;
            $card->saveOrFail();
            throw ValidationException::withMessages([
                'card_id' => 'The card balance must be greater than 200 000 UZS',
            ]);
        }

        return $card;
    }
}
