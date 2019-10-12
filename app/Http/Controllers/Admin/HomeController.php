<?php

namespace App\Http\Controllers\Admin;

use App\Model\DiagnosticLabInformation;
use App\Model\User;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    //
    public function viewDashboardHomePage()
    {
        $noOfRegisteredUsers = User::all()->count();
        $noOfRegisteredLabs = DiagnosticLabInformation::all()->count();
        return view('admin.layouts.template', compact('noOfRegisteredUsers', 'noOfRegisteredLabs'));
    }
}
