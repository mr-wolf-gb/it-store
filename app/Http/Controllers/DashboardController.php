<?php

namespace App\Http\Controllers;

use App\Models\SupportResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard', [
            'stats' => [
                'resources_total' => SupportResource::count(),
                'resources_public' => SupportResource::query()->where('visibility', 'public')->count(),
                'resources_private' => SupportResource::query()->where('visibility', 'private')->count(),
                'roles_total' => Role::count(),
                'users_total' => User::count(),
            ],
            'recentResources' => SupportResource::query()
                ->latest('updated_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
