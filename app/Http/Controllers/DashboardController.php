<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function getDashboard()
    {
        return view('user.dashboard.home');
    }

    public function getDashboardSettings()
    {
        return view('user.dashboard.settings');
    }

}
