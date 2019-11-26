<?php

namespace App\Http\Controllers\Admin;

use App\Model\DiagnosticLabInformation;
use App\Model\User;
use App\Http\Controllers\Controller;
use App\Respositories\DiagnosticLabInformationRepository;
use App\Respositories\UserRepository;
use Auth;

class HomeController extends Controller
{
    //
    public function viewDashboardHomePage()
    {
        $user = Auth::user();
//        dd($user);
        $noOfRegisteredUsers = (new UserRepository())->all()->count();
        $noOfRegisteredLabs = (new DiagnosticLabInformationRepository())->all()->count();
        return view('admin.layouts.template', compact('noOfRegisteredUsers', 'noOfRegisteredLabs'));
    }
}
