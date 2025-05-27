<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\Models\Company\InstallmentGuarantor;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentGuarantorGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentGuarantorGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGuarantor $root) => $root->id,
            ],
            'installment_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGuarantor $root) => $root->installment_id,
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (InstallmentGuarantor $root) => $root->installment,
            ],
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGuarantor $root) => $root->customer_id,
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (InstallmentGuarantor $root) => $root->customer,
            ],
            'any_card_charge' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (InstallmentGuarantor $root) => $root->any_card_charge,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGuarantor $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGuarantor $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentGuarantor $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
