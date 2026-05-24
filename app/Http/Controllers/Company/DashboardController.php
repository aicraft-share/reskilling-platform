<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect to announcements as the main dashboard for companies
        return redirect()->route('company.announcements.index');
    }
}
