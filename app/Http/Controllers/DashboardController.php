<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard() {
    	$user = Auth::user()->user;

    	$reservations = $user->reservations()->current()->hasExistingDevice()->get();

        return view('user.dashboard.home',compact('reservations'));
    }
}