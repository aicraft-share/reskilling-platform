<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminOperationLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminOperationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminOperationLog::query()->latest('created_at');

        if ($request->filled('actor_admin_id')) {
            $query->where('actor_admin_id', $request->integer('actor_admin_id'));
        }

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->input('action_type'));
        }

        if ($request->filled('target_type')) {
            $query->where('target_type', $request->input('target_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('target_label', 'like', "%{$keyword}%")
                    ->orWhere('actor_admin_name', 'like', "%{$keyword}%")
                    ->orWhere('target_type', 'like', "%{$keyword}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        $admins = User::where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']);

        return view('admin.operation_logs.index', compact('logs', 'admins'));
    }

    public function show(AdminOperationLog $operationLog)
    {
        return view('admin.operation_logs.show', compact('operationLog'));
    }
}
