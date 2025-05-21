<?php

namespace App\GraphQL\QrPay\Types;

use App\Models\Company\SbpTransaction;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SbpTransactionGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SbpTransactionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SbpTransaction $root) => $root->id,
            ],
            'terminal_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SbpTransaction $root) => $root->terminal_id,
            ],
            'ext_id' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (SbpTransaction $root) => $root->ext_id,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SbpTransaction $root) => $root->status->value,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (SbpTransaction $root) => $root->amount,
            ],
            'credit' => [
                'type' => GraphQL::type(SbpPaymentCreditGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (SbpTransaction $root) => $root->credit,
            ],
            'debit' => [
                'type' => GraphQL::type(SbpPaymentDebitGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (SbpTransaction $root) => $root->debit,
            ],
            'sender' => [
                'type' => GraphQL::type(SbpPaymentSenderGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (SbpTransaction $root) => $root->sender,
            ],
            'receiver' => [
                'type' => GraphQL::type(SbpPaymentReceiverGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (SbpTransaction $root) => $root->receiver,
            ],
            'payment_updated_at' => [
                'type' => Type::int(),
                'resolve' => fn (SbpTransaction $root) => $root->payment_updated_at?->timestamp,
            ],
            'currency' => [
                'type' => Type::float(),
                'resolve' => fn (SbpTransaction $root) => $root->currency,
            ],
            'qr_link' => [
                'type' => Type::string(),
                'resolve' => fn (SbpTransaction $root) => $root->qr_link,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SbpTransaction $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SbpTransaction $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
