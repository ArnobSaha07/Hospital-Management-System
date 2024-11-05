<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Exception;

/**
 * Class DashboardRepository
 */
class DashboardRepository
{
    /**
     * @throws Exception
     */
    public function getIncomeExpenseReport(array $input): array
    {
        $dates = $this->getDate($input['start_date'], $input['end_date']);

        $incomes = Income::all();
        $expenses = Expense::all();

        //Income report
        $data = [];
        foreach ($dates['dateArr'] as $cDate) {
            $incomeTotal = 0;
            foreach ($incomes as $row) {
                $chartDates = $cDate;
                $incomeDates = trim(substr($row['date'], 0, 10));
                if ($chartDates == $incomeDates) {
                    $incomeTotal += $row['amount'];
                }
            }
            $incomeTotalArray[] = $incomeTotal;
            $dateArray[] = $cDate;
        }

        //Expense report
        foreach ($dates['dateArr'] as $cDate) {
            $expenseTotal = 0;
            foreach ($expenses as $row) {
                $chartDates = $cDate;
                $expenseDates = trim(substr($row['date'], 0, 10));
                if ($chartDates == $expenseDates) {
                    $expenseTotal += $row['amount'];
                }
            }
            $expenseTotalArray[] = $expenseTotal;
        }

        $data['incomeTotal'] = $incomeTotalArray;
        $data['expenseTotal'] = $expenseTotalArray;
        $data['date'] = $dateArray;

        return $data;
    }

    /**
     * @throws Exception
     */
    public function getDate(string $startDate, string $endDate): array
    {
        $dateArr = [];
        $subStartDate = '';
        $subEndDate = '';
        if (! ($startDate && $endDate)) {
            $data = [
                'dateArr' => $dateArr,
                'startDate' => $subStartDate,
                'endDate' => $subEndDate,
            ];

            return $data;
        }
        $end = trim(substr($endDate, 0, 10));
        $start = Carbon::parse($startDate)->toDateString();
        /** @var \Illuminate\Support\Carbon $startDate */
        $startDate = Carbon::createFromFormat('Y-m-d', $start);
        /** @var \Illuminate\Support\Carbon $endDate */
        $endDate = Carbon::createFromFormat('Y-m-d', $end);

        while ($startDate <= $endDate) {
            $dateArr[] = $startDate->copy()->format('Y-m-d');
            $startDate->addDay();
        }
        $start = current($dateArr);
        $endDate = end($dateArr);
        $subStartDate = Carbon::parse($start)->startOfDay()->format('Y-m-d H:i:s');
        $subEndDate = Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s');

        $data = [
            'dateArr' => $dateArr,
            'startDate' => $subStartDate,
            'endDate' => $subEndDate,
        ];

        return $data;
    }

    /**
     * @return int[]
     */
    public function getTotalActiveDeActiveHospitalPlans(): array
    {
        $activePlansCount = 0;
        $deActivePlansCount = 0;
        $subscriptions = Subscription::whereStatus(Subscription::ACTIVE)->get();
        foreach ($subscriptions as $sub) {
            if (! $sub->isExpired()) {   // active plans
                $activePlansCount++;
            } else {
                $deActivePlansCount++;
            }
        }

        return ['activePlansCount' => $activePlansCount, 'deActivePlansCount' => $deActivePlansCount];
    }

    public function totalFilterDay($formatStartDate, $formatEndDate): array
    {
        $transactions = Transaction::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total_amount'))
            ->where('status', Transaction::APPROVED)
            ->whereBetween('created_at', [$formatStartDate, $formatEndDate])
            ->groupBy('date')
            ->get();

        $transactionMap = [];
        foreach ($transactions as $transaction) {
            $transactionMap[$transaction->date] = $transaction->total_amount;
        }

        $period = CarbonPeriod::create($formatStartDate, $formatEndDate);
        $dateArr = [];
        $income = [];

        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');
            $dateArr[] = $date->format('d-m-y');
            $income[] = $transactionMap[$dateKey] ?? 0;
        }

        $data['days'] = $dateArr;
        $data['income'] = [
            'label' => trans('messages.income', [], getLoggedInUser()->language),
            'data' => $income,
            'fill' => 'false',
            'borderColor' => 'rgb(153, 102, 255)',
            'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
            'borderWidth' => 1,
            'tension' => 0.4,
        ];

        return $data;
    }
}
