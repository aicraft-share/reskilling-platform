<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companies = $user->assignedCompanies;

        // Fetch upcoming meetings for companies assigned to this teacher
        // OR meetings created by this teacher (though usually they are linked to company)
        $upcomingMeetings = \App\Models\Meeting::whereHas('company', function ($query) use ($user) {
            $query->whereHas('teachers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        })
            ->orWhere('created_by', $user->id) // Just in case
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('companies', 'upcomingMeetings'));
    }
}
