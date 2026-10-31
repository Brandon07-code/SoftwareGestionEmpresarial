<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-reportes'),
        ];
    }

    public function index(Request $request)
    {
        $activities = Activity::query()
            ->with('causer')
            ->when($request->filled('log_name'), fn ($q) => $q->where('log_name', $request->input('log_name')))
            ->when($request->filled('event'), fn ($q) => $q->where('event', $request->input('event')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('causer_id', $request->integer('user_id')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($q) use ($search) {
                    $q->where('description', 'like', $search)
                        ->orWhere('properties', 'like', $search);
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $modules = Activity::select('log_name')->distinct()->pluck('log_name');
        $users = User::orderBy('name')->get(['id', 'name']);

        $totalSecurity = Activity::where('log_name', 'seguridad')->count();
        $totalBusiness = Activity::where('log_name', '!=', 'seguridad')->count();

        return view('audit.index', [
            'activities' => $activities,
            'modules' => $modules,
            'users' => $users,
            'totalActivities' => Activity::count(),
            'totalSecurity' => $totalSecurity,
            'totalBusiness' => $totalBusiness,
        ]);
    }
}
