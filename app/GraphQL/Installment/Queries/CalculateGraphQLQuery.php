<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\CalculatedGraphGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
use App\Models\Company\Tariff;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CalculateGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'calculate',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(CalculatedGraphGraphQLType::GRAPHQL_TYPE_NAME))));
    }

    public function args(): array
    {
        return [
            'tariff_id' => [
                'name' => 'tariff_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', Rule::exists('tariffs', 'id')->whereNull('deleted_at')],
            ],
            'amount' => [
                'name' => 'amount',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|numeric',
            ],
            'initial_fee' => [
                'name' => 'initial_fee',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|numeric',
            ],
            'period' => [
                'name' => 'period',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CALCULATE_INSTALLMENT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Tariff $tariff */
        $tariff = Tariff::query()->where('id', $args['tariff_id'])->firstOrFail();
        $amount = $args['amount'];
        $amount -= $args['initial_fee'];
        $portion = $amount * $tariff->percent / 100;

        $currentDate = Carbon::now();
        $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($args['period'] - 1));

        $calculated = [];
        $i = 1;
        foreach ($periods as $period) {
            $calculated[] = [
                'id' => $i,
                'date' => $period->format('Y-m-d'),
                'amount' => $amount / $args['period'],
                'portion' => $portion,
                'monthly' => $amount / $args['period'] + $portion,
            ];
            $i++;
        }

        return $calculated;
    }
}
