<?php

namespace App\Http\Controllers;

use App\Models\ResourceCategory;
use App\Models\SupportResourceFile;
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
                'resources_draft' => SupportResource::query()->where('status', 'draft')->count(),
                'resources_published' => SupportResource::query()->where('status', 'published')->count(),
                'resources_archived' => SupportResource::query()->where('status', 'archived')->count(),
                'resources_featured' => SupportResource::query()->where('is_featured', true)->count(),
                'roles_total' => Role::count(),
                'users_total' => User::count(),
                'total_downloads' => SupportResource::query()->sum('download_count'),
                'files_total' => SupportResourceFile::count(),
                'storage_bytes' => (int) SupportResourceFile::query()->sum('file_size'),
            ],
            'recentResources' => SupportResource::query()
                ->with(['files', 'category'])
                ->latest('updated_at')
                ->limit(8)
                ->get(),
            'topCategories' => ResourceCategory::query()
                ->withCount('resources')
                ->orderByDesc('resources_count')
                ->limit(5)
                ->get(),
            'topDownloadedResources' => SupportResource::query()
                ->where('status', 'published')
                ->orderByDesc('download_count')
                ->limit(5)
                ->get(),
        ]);
    }
}
