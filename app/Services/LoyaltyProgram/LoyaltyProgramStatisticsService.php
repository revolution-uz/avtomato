<?php

namespace App\Services\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramStatisticsService
{
    private ?Carbon $from;
    private ?Carbon $to;
    private ?Collection $allDates = null;
    private Builder $joinQuery;
    private Expression $startDateExpression;
    private Expression $startDateExpressionWithAlias;
    private Expression $userCountExpressionWithAlias;

    public function __construct(?Carbon $from = null, ?Carbon $to = null)
    {
        $this->from = $from ?: Carbon::now()->subMonth();
        $this->to = $to ?: Carbon::now();

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

    public function getUsersChart(): array
    {
        return $this->fillMissingDates(
            User::query()
                ->select($this->userCountExpressionWithAlias, DB::raw('DATE(created_at) as date'))
                ->whereBetween('created_at', [$this->from, $this->to])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        );
    }

    public function getSubscribedUsersChart(): array
    {
        return $this->fillMissingDates(
            $this->joinQuery
                ->select($this->userCountExpressionWithAlias, $this->startDateExpressionWithAlias)
                ->whereBetween($this->startDateExpression, [$this->from, $this->to])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        );
    }

    public function getExtendedSubscriptionUsersChart(): array
    {
        return $this->fillMissingDates(
            $this->joinQuery
                ->select($this->userCountExpressionWithAlias, $this->startDateExpressionWithAlias)
                ->whereRaw('DATE_PART(\'days\', loyalty_program_subscriptions.created_at - loyalty_program_subscriptions.expired_at) > loyalty_program_tariffs.subscription_days')
                ->whereBetween($this->startDateExpression, [$this->from, $this->to])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        );
    }

    public function getNonExtendedSubscriptionUsersChart(): array
    {
        return $this->fillMissingDates(
            User::query()
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
        );
    }

    private function fillMissingDates($data)
    {
        $allDates = $this->getAllDates();

        return $allDates->map(fn ($date) => $data
            ->firstWhere('date', $date) ?? ['date' => $date, 'count' => 0]
        )->toArray();
    }

    private function getAllDates()
    {
        if (! $this->allDates) {
            $this->allDates = collect();

            for ($date = clone $this->from; $date->lte($this->to); $date->addDay()) {
                $this->allDates->push($date->toDateString());
            }
        }

        return $this->allDates;
    }
}
