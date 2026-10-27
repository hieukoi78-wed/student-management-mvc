<?php

namespace App\Controllers;

use App\Models\SalesReportModel;
use DateTimeImmutable;
use DateTimeZone;

class ReportsController extends BaseController
{
    public function index()
    {
        $timezone = new DateTimeZone('Asia/Ho_Chi_Minh');
        $now = new DateTimeImmutable('now', $timezone);
        $weekStart = $now->modify('monday this week')->setTime(0, 0);
        $nextWeek = $weekStart->modify('+1 week');
        $monthStart = $now->modify('first day of this month')->setTime(0, 0);
        $nextMonth = $monthStart->modify('+1 month');
        $quarterNumber = (int) ceil((int) $now->format('n') / 3);
        $quarterStart = $now->setDate((int) $now->format('Y'), (($quarterNumber - 1) * 3) + 1, 1)->setTime(0, 0);
        $nextQuarter = $quarterStart->modify('+3 months');
        $report = new SalesReportModel();
        $weekStartText = $weekStart->format('Y-m-d H:i:s');
        $nextWeekText = $nextWeek->format('Y-m-d H:i:s');
        $hourlySales = $report->hourlySales($weekStartText, $nextWeekText);
        $hourTotals = array_fill(0, 24, 0);
        foreach ($hourlySales as $hour) {
            $hourTotals[(int) $hour['sale_hour']] = (int) $hour['quantity_sold'];
        }

        return view('reports/index', [
            'week' => $report->revenueBetween($weekStartText, $nextWeekText),
            'month' => $report->revenueBetween($monthStart->format('Y-m-d H:i:s'), $nextMonth->format('Y-m-d H:i:s')),
            'quarter' => $report->revenueBetween($quarterStart->format('Y-m-d H:i:s'), $nextQuarter->format('Y-m-d H:i:s')),
            'weekLabel' => $weekStart->format('d/m/Y') . ' – ' . $nextWeek->modify('-1 day')->format('d/m/Y'),
            'monthLabel' => $now->format('m/Y'),
            'quarterLabel' => 'Quý ' . $quarterNumber . '/' . $now->format('Y'),
            'busiestHour' => $report->busiestHour($weekStartText, $nextWeekText),
            'hourTotals' => $hourTotals,
            'topBooks' => $report->topBooks($weekStartText, $nextWeekText),
        ]);
    }
}
