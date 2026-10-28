<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\BookModel;
use App\Models\SalesReportModel;
use DateTimeImmutable;
use DateTimeZone;

class DashboardController extends BaseController
{
    public function index()
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $weekStart = $now->modify('monday this week')->setTime(0, 0);
        $nextWeek = $weekStart->modify('+1 week');
        $dailyRevenue = array_fill_keys(
            array_map(
                static fn (int $offset): string => $weekStart->modify("+{$offset} days")->format('Y-m-d'),
                range(0, 6)
            ),
            0.0
        );

        foreach ((new SalesReportModel())->dailyRevenue(
            $weekStart->format('Y-m-d H:i:s'),
            $nextWeek->format('Y-m-d H:i:s')
        ) as $day) {
            $dailyRevenue[$day['sale_date']] = (float) ($day['revenue'] ?? 0);
        }

        $weekRevenue = (float) array_sum(array_map(static fn ($value): float => (float) $value, $dailyRevenue));

        return view('dashboard', [
            'categoryCount' => (new CategoryModel())->countAllResults(),
            'bookCount' => (new BookModel())->countAllResults(),
            'lowStockCount' => (new BookModel())->where('stock <', 5)->countAllResults(),
            'dailyRevenue' => $dailyRevenue,
            'weekRevenue' => $weekRevenue,
        ]);
    }
}
