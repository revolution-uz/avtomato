<?php

namespace App\Services\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgram\LoyaltyProgramStatisticsChartEnum;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramStatisticsService
{
    private ?string $from;
    private ?string $to;
    private Builder $joinQuery;
    private Expression $startDateExpression;
    private Expression $startDateExpressionWithAlias;
    private Expression $userCountExpressionWithAlias;
    private LoyaltyProgramStatisticsChartEnum $chartType;

    public function __construct(int $chart_type, ?string $from = null, ?string $to = null)
    {
        $this->chartType = LoyaltyProgramStatisticsChartEnum::from($chart_type);
        $this->from = $from ?: Carbon::now()->subMonth()->format('Y-m-d');
        $this->to = $to ?: Carbon::now()->format('Y-m-d');

        $this->joinQuery = User::query()
            ->leftJoin(
                'loyalty_program_subscriptions',
                'loyalty_program_users.id', '=', 'loyalty_program_subscriptions.user_id'
            )
            ->leftJoin(
                'loyalty_program_tariffs',
                'loyalty_program_subscriptions.tariff_id', '=', 'loyalty_program_tariffs.id'
            );

        $this->startDateExpression = DB::raw('loyalty_program_subscriptions.expired_at - loyalty_program_tariffs.subscription_days * INTERVAL \'1 day\'');
        $this->startDateExpressionWithAlias = DB::raw('DATE(loyalty_program_subscriptions.expired_at - loyalty_program_tariffs.subscription_days * INTERVAL \'1 day\') as date');
        $this->userCountExpressionWithAlias = DB::raw('COUNT(loyalty_program_users.id) as count ');
    }

    public function getUsers()
    {
        $usersCount = User::query()->count();
        $rangedUsersCount = User::query()->whereBetween('created_at', [$this->from, $this->to])->clone()->count();

        return [
            'ranged_count' => $rangedUsersCount,
            'count' => $usersCount,
        ];
    }

    public function getSubscribedUsers()
    {
        $userWhichHasSubscription = User::query()
            ->whereHas('subscriptions')
            ->count();

        $rangedUserWhichHasSubscription = $this->joinQuery
            ->whereBetween($this->startDateExpression, [$this->from, $this->to])
            ->count();

        return [
            'ranged_count' => $rangedUserWhichHasSubscription,
            'count' => $userWhichHasSubscription,
        ];
    }

    public function getExtendedSubscriptionUsers()
    {
        $usersWhichHasExtendedSubscription = $this->joinQuery
            ->whereRaw('DATE_PART(\'days\', loyalty_program_subscriptions.created_at - loyalty_program_subscriptions.expired_at) > loyalty_program_tariffs.subscription_days')
            ->count();

        $rangedUsersWhichHasExtendedSubscription = $this->joinQuery
            ->whereRaw('DATE_PART(\'days\', loyalty_program_subscriptions.created_at - loyalty_program_subscriptions.expired_at) > loyalty_program_tariffs.subscription_days')
            ->whereBetween($this->startDateExpression, [$this->from, $this->to])
            ->count();

        return [
            'ranged_count' => $rangedUsersWhichHasExtendedSubscription,
            'count' => $usersWhichHasExtendedSubscription,
        ];
    }

    public function getNonExtendedSubscriptionUsers()
    {
        $usersWhichHasNoExtendedSubscription = User::query()
            ->whereHas('subscriptions', fn ($query) => $query
                ->where('expired_at', '<', Carbon::now()))
            ->count();

        $rangedUsersWhichHasNoExtendedSubscription = User::query()
            ->whereHas('subscriptions', fn ($query) => $query
                ->where('expired_at', '<', Carbon::now())
                ->whereBetween('expired_at', [$this->from, $this->to]))
            ->count();

        return [
            'ranged_count' => $rangedUsersWhichHasNoExtendedSubscription,
            'count' => $usersWhichHasNoExtendedSubscription,
        ];
    }

    public function getChartData(): array
    {
        return match ($this->chartType) {
            LoyaltyProgramStatisticsChartEnum::USERS_CHART => $this->getUsersChart(),
            LoyaltyProgramStatisticsChartEnum::SUBSCRIBED_USERS_CHART => $this->getSubscribedUsersChart(),
            LoyaltyProgramStatisticsChartEnum::EXTENDED_SUBSCRIPTION_USERS_CHART => $this->getExtendedSubscriptionUsersChart(),
            LoyaltyProgramStatisticsChartEnum::NON_EXTENDED_SUBSCRIPTION_USERS_CHART => $this->getNonExtendedSubscriptionUsersChart(),
        };
    }

    private function getUsersChart(): array
    {
        return User::query()
            ->select($this->userCountExpressionWithAlias, DB::raw('DATE(created_at) as date'))
            ->whereBetween('created_at', [$this->from, $this->to])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getSubscribedUsersChart(): array
    {
        return $this->joinQuery
            ->select($this->userCountExpressionWithAlias, $this->startDateExpressionWithAlias)
            ->whereBetween($this->startDateExpression, [$this->from, $this->to])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getExtendedSubscriptionUsersChart(): array
    {
        return $this->joinQuery
            ->select($this->userCountExpressionWithAlias, $this->startDateExpressionWithAlias)
            ->whereRaw('DATE_PART(\'days\', loyalty_program_subscriptions.created_at - loyalty_program_subscriptions.expired_at) > loyalty_program_tariffs.subscription_days')
            ->whereBetween($this->startDateExpression, [$this->from, $this->to])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getNonExtendedSubscriptionUsersChart(): array
    {
        return User::query()
            ->rightJoin(
                'loyalty_program_subscriptions',
                'loyalty_program_users.id', '=', 'loyalty_program_subscriptions.user_id'
            )
            ->select($this->userCountExpressionWithAlias, DB::raw('DATE(loyalty_program_subscriptions.expired_at) as date'))
            ->where('loyalty_program_subscriptions.expired_at', '<', Carbon::now())
            ->whereBetween('loyalty_program_subscriptions.expired_at', [$this->from, $this->to])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
