<?php

namespace Modules\School\Services\Dashboard;

use Modules\School\Enums\UserRoleEnum;
use Modules\School\Models\Company;
use Modules\School\Models\User;
use Modules\School\Support\SchoolTenancy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Read-only overview for the Admin dashboard landing page: this school, a headcount per
 * role, and how those headcounts have moved — all from `users.created_at`, which is the
 * only time-series data we actually have. No fabricated metrics (there's no "sessions" or
 * "conversions" table here) — every number is a real count, scoped the same way
 * UserService is (current domain's school only).
 */
class DashboardService
{
    /** How many days of daily new-signup history the trend chart and sparklines cover. */
    private const TREND_DAYS = 30;

    /**
     * @return array{
     *     school: Company,
     *     counts: array<string, int>,
     *     newThisWeek: array<string, int>,
     *     trendDates: array<int, string>,
     *     trendByRole: array<string, array<int, int>>,
     *     trendTotal: array<int, int>,
     *     recentUsers: Collection<int, User>,
     * }
     */
    public function summary(): array
    {
        try {
            $school = SchoolTenancy::company();

            if (! $school) {
                throw new RuntimeException('No active school for this request.');
            }

            $counts = User::query()
                ->where('company_id', $school->id)
                ->selectRaw('role, count(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role');

            $since = Carbon::today()->subDays(self::TREND_DAYS - 1);

            $newThisWeek = User::query()
                ->where('company_id', $school->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->selectRaw('role, count(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role');

            // ->toBase() before ->get(): plain stdClass rows, not hydrated User models —
            // otherwise 'role' below would come back as the cast UserRoleEnum instance
            // (unusable as an array/pluck key) instead of the raw string.
            $daily = User::query()
                ->where('company_id', $school->id)
                ->where('created_at', '>=', $since)
                ->selectRaw('DATE(created_at) as day, role, count(*) as total')
                ->groupBy('day', 'role')
                ->toBase()
                ->get()
                ->groupBy('day')
                ->map(fn ($rows) => $rows->pluck('total', 'role'));

            $trendDates = [];
            $trendByRole = collect(UserRoleEnum::values())->mapWithKeys(fn (string $role) => [$role => []])->all();

            for ($date = $since->copy(); $date->lte(Carbon::today()); $date->addDay()) {
                $key = $date->toDateString();
                $trendDates[] = $date->format('M j');

                foreach (UserRoleEnum::values() as $role) {
                    $trendByRole[$role][] = (int) ($daily->get($key)?->get($role) ?? 0);
                }
            }

            $trendTotal = [];
            for ($i = 0, $days = count($trendDates); $i < $days; $i++) {
                $trendTotal[] = array_sum(array_column($trendByRole, $i));
            }

            $recentUsers = User::query()
                ->where('company_id', $school->id)
                ->latest()
                ->limit(5)
                ->get();

            return [
                'school' => $school,
                'counts' => collect(UserRoleEnum::values())
                    ->mapWithKeys(fn (string $role) => [$role => (int) ($counts[$role] ?? 0)])
                    ->all(),
                'newThisWeek' => collect(UserRoleEnum::values())
                    ->mapWithKeys(fn (string $role) => [$role => (int) ($newThisWeek[$role] ?? 0)])
                    ->all(),
                'trendDates' => $trendDates,
                'trendByRole' => $trendByRole,
                'trendTotal' => $trendTotal,
                'recentUsers' => $recentUsers,
            ];
        } catch (Throwable $e) {
            Log::error('DashboardService::summary failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
