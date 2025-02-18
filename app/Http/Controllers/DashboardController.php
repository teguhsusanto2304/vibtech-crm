<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('dashboard', compact('user'))->with('title', 'Dashboard')->with('breadcrumb', ['Home','Dashboard']);
    }
}
