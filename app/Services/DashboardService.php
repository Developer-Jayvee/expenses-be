<?php

namespace App\Services;

use App\Enums\BillFrequencyEnum;
use App\Enums\BillStatusEnum;
use App\Models\BillsModel;
use App\Models\DailyBudgetsModel;
use App\Models\TransactionsModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardService extends BaseCrudService
{
    private const UPCOMING_STATUSES = [BillStatusEnum::ACTIVE, BillStatusEnum::ONGOING];

    /**
     * Dashboard summary: monthly expense totals for the current year, bill
     * totals per category, and bills due this month. Computed fresh on
     * every request.
     */
    public function getSummary(): JsonResponse
    {
        $year = (int) now()->year;

        $summary = [
            'year' => $year,
            'monthly_expenses' => $this->monthlyExpenses($year),
            'bills_by_category' => $this->billsByCategory(),
            'upcoming_bills' => $this->upcomingBills(),
        ];

        return $this->successMessage('Dashboard summary fetched.', $summary);
    }

    /**
     * Total transaction + daily expense amount per month (January-December)
     * for the given year, scoped to the authenticated user.
     *
     * @return array<int, array{month: int, label: string, total: float}>
     */
    private function monthlyExpenses(int $year): array
    {
        $transactionTotals = TransactionsModel::query()
            ->whereYear('transaction_date', $year)
            ->selectRaw('MONTH(transaction_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $dailyExpenseTotals = DailyBudgetsModel::query()
            ->join('daily_expenses', 'daily_expenses.daily_budget_id', '=', 'daily_budgets.id')
            ->whereYear('daily_budgets.budget_date', $year)
            ->selectRaw('MONTH(daily_budgets.budget_date) as month, SUM(daily_expenses.amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return collect(range(1, 12))->map(fn (int $month) => [
            'month' => $month,
            'label' => Carbon::create($year, $month, 1)->format('M'),
            'total' => (float) ($transactionTotals[$month] ?? 0) + (float) ($dailyExpenseTotals[$month] ?? 0),
        ])->values()->all();
    }

    /**
     * Total bill amount per category, largest first.
     *
     * @return array<int, array{category: string, label: string, total: float}>
     */
    private function billsByCategory(): array
    {
        $totals = BillsModel::query()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return $totals->map(fn ($row) => [
            'category' => $row->category?->value ?? 'uncategorized',
            'label' => $row->category?->label() ?? 'Uncategorized',
            'total' => (float) $row->total,
        ])->values()->all();
    }

    /**
     * Active/ongoing bills whose next due date falls within the current
     * calendar month, soonest first. Bills missing a valid frequency
     * (legacy data predating that field being required) have no
     * computable due date and are skipped rather than crashing the
     * summary.
     *
     * @return array<int, array{id: int, name: string, amount: float, category: string, category_label: string, due_date: string, status: string}>
     */
    private function upcomingBills(): array
    {
        $now = now();

        return BillsModel::query()
            ->whereIn('status', array_map(fn ($status) => $status->value, self::UPCOMING_STATUSES))
            ->get()
            ->filter(fn (BillsModel $bill) => BillFrequencyEnum::tryFrom($bill->frequency) !== null)
            ->map(fn (BillsModel $bill) => [
                'bill' => $bill,
                'due_date' => BillService::billingDate($bill),
            ])
            ->filter(fn (array $entry) => $entry['due_date']->isSameMonth($now) && $entry['due_date']->isSameYear($now))
            ->sortBy(fn (array $entry) => $entry['due_date'])
            ->map(fn (array $entry) => [
                'id' => $entry['bill']->id,
                'name' => $entry['bill']->name,
                'amount' => (float) $entry['bill']->amount,
                'category' => $entry['bill']->category?->value,
                'category_label' => $entry['bill']->category?->label(),
                'due_date' => $entry['due_date']->format('Y-m-d'),
                'status' => $entry['bill']->status?->value,
            ])
            ->values()
            ->all();
    }
}
