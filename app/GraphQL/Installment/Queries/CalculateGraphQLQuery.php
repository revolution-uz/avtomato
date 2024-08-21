<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\CalculatedGraphGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
use App\Models\Company\Tariff;
use App\Services\Installment\InstallmentService;
use Carbon\Carbon;
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
            'starts_in' => [
                'name' => 'starts_in',
                'type' => Type::string(),
                'rules' => [
                    'nullable',
                    'date',
                ],
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
        $period = $args['period'];
        $amount -= $args['initial_fee'];
        $rate = $tariff->percent / 100 / 12;

        $currentDate = Carbon::now();
        if (isset($args['starts_in'])) {
            $currentDate = Carbon::make($args['starts_in']);
        }
        $periods = InstallmentService::generatePeriods($currentDate, $period);

        $calculated = [];
        $i = 1;

        if ($tariff->is_annual) {
            if ($rate == 0) {
                $monthly = $amount / $period;
            } else {
                $monthly = ($amount * $rate * pow(1 + $rate, $period)) / (pow(1 + $rate, $period) - 1);
            }

            foreach ($periods as $period) {
                $calculated[] = [
                    'id' => $i,
                    'date' => $period->format('Y-m-d'),
                    'amount' => $amount * $rate,
                    'portion' => $monthly - $amount * $rate,
                    'monthly' => $monthly,
                ];
                $amount -= $monthly - $amount * $rate;
                $i++;
            }
        } else {
            $monthlyAmount = $amount / $period;
            foreach ($periods as $period) {
                $calculated[] = [
                    'id' => $i,
                    'date' => $period->format('Y-m-d'),
                    'amount' => $monthlyAmount,
                    'portion' => $amount * $rate,
                    'monthly' => $monthlyAmount + $amount * $rate,
                ];
                $amount -= $monthlyAmount;
                $i++;
            }
        }

        return $calculated;
    }
}
