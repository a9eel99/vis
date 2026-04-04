<?php

namespace App\Http\Controllers\AuditLog;

use App\Domain\Models\AuditLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'model']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('model_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($type = $request->get('type')) {
            $query->where('model_type', strtolower($type));
        }

        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->latest()->paginate(30);
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('audit-logs.index', compact('logs', 'actions'));
    }
}