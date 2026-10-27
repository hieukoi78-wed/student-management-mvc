<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\BookModel;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('dashboard', [
            'categoryCount' => (new CategoryModel())->countAllResults(),
            'bookCount' => (new BookModel())->countAllResults(),
            'lowStockCount' => (new BookModel())->where('stock <', 5)->countAllResults(),
        ]);
    }
}
