<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Meeting::select('meetings.*')
            ->with([
                'company' => fn($q) => $q->withTrashed(),
                'creator' => fn($q) => $q->withTrashed(),
                'participants'
            ])
            ->leftJoin('companies', 'meetings.company_id', '=', 'companies.id');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('meetings.title', 'like', '%' . $request->search . '%')
                    ->orWhere('companies.name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->sort === 'company_name') {
            $direction = $request->input('direction', 'asc');
            $query->orderBy('companies.name', $direction);
        } else {
            $this->applySorting($query, $request, ['title', 'scheduled_at'], 'scheduled_at', 'desc');
        }

        $meetings = $query->paginate(15)->withQueryString();

        return view('admin.meetings.index', compact('meetings'));
    }
}
