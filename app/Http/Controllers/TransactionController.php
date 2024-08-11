<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function getTransactionByDate(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $startDate = (new DateTime())->setDate($year, $month, 1)->format('Y-m-d');
        $endDate = (new DateTime())->setDate($year, $month, 1)->modify('last day of this month')->format('Y-m-d');
        $endDate = ($endDate > date('Y-m-d')) ? date('Y-m-d') : $endDate;

        $dates = [];
        $incomes = [];

        Log::info('API Response');
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dates[] = $day = date('j', strtotime($currentDate));;
            $totalIncome = Transaction::whereDate('created_at', $currentDate)->sum('final_price');
            $incomes[] = $totalIncome;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        $startDateFormatted = to_date_string($startDate, false);
        $endDateFormatted = to_date_string($endDate, false);
        return response()->json([
            'dates' => $dates,
            'incomes' => $incomes,
            'startDate' => $startDateFormatted,
            'endDate' => $endDateFormatted
        ]);
    }
}
