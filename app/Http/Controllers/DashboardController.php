<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Movie::count();
        return view('admin.dashboard.home.index', compact('total'));
    }
}
