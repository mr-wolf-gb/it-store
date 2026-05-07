<?php

namespace App\Http\Controllers;

use App\Models\SupportResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $canViewPrivate = $request->user()?->can('resources.view_private')
            || $request->user()?->can('resources.manage');

        $search = $request->string('q')->toString();
        $resourceType = $request->string('resource_type')->toString();
        $visibility = $request->string('visibility')->toString();

        $resources = SupportResource::query()
            ->with('uploader')
            ->when(! $canViewPrivate, fn ($query) => $query->where('visibility', 'public'))
            ->when(
                $canViewPrivate && in_array($visibility, SupportResource::VISIBILITY_OPTIONS, true),
                fn ($query) => $query->where('visibility', $visibility)
            )
            ->when(
                in_array($resourceType, SupportResource::TYPE_OPTIONS, true),
                fn ($query) => $query->where('resource_type', $resourceType)
            )
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('library.index', [
            'resources' => $resources,
            'canViewPrivate' => $canViewPrivate,
            'types' => SupportResource::TYPE_OPTIONS,
            'filters' => [
                'q' => $search,
                'resource_type' => $resourceType,
                'visibility' => $visibility,
            ],
        ]);
    }

    public function download(SupportResource $supportResource): RedirectResponse|StreamedResponse
    {
        Gate::authorize('download', $supportResource);

        if ($supportResource->source_type === 'link') {
            abort_if(blank($supportResource->link_url), 404);

            return Str::startsWith($supportResource->link_url, ['http://', 'https://'])
                ? redirect()->away($supportResource->link_url)
                : redirect($supportResource->link_url);
        }

        abort_if(blank($supportResource->file_path), 404);

        $disk = Storage::disk('local');
        abort_if(! $disk->exists($supportResource->file_path), 404);

        return $disk->download(
            $supportResource->file_path,
            $supportResource->file_name ?: basename($supportResource->file_path),
        );
    }
}
