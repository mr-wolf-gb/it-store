<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportResourceRequest;
use App\Http\Requests\UpdateSupportResourceRequest;
use App\Models\SupportResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SupportResourceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();
        $resourceType = $request->string('resource_type')->toString();
        $visibility = $request->string('visibility')->toString();

        $resources = SupportResource::query()
            ->with('uploader')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($resourceType, SupportResource::TYPE_OPTIONS, true),
                fn ($query) => $query->where('resource_type', $resourceType)
            )
            ->when(
                in_array($visibility, SupportResource::VISIBILITY_OPTIONS, true),
                fn ($query) => $query->where('visibility', $visibility)
            )
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.resources.index', [
            'resources' => $resources,
            'types' => SupportResource::TYPE_OPTIONS,
            'filters' => [
                'q' => $search,
                'resource_type' => $resourceType,
                'visibility' => $visibility,
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.resources.create', [
            'types' => SupportResource::TYPE_OPTIONS,
            'visibilities' => SupportResource::VISIBILITY_OPTIONS,
            'sources' => SupportResource::SOURCE_OPTIONS,
        ]);
    }

    public function store(StoreSupportResourceRequest $request): RedirectResponse
    {
        $supportResource = new SupportResource;
        $supportResource->user_id = $request->user()->id;

        $this->applyPayload($supportResource, $request);
        $supportResource->save();

        return redirect()
            ->route('admin.resources.index')
            ->with('status', 'Resource created successfully.');
    }

    public function edit(SupportResource $supportResource): View
    {
        return view('admin.resources.edit', [
            'supportResource' => $supportResource,
            'types' => SupportResource::TYPE_OPTIONS,
            'visibilities' => SupportResource::VISIBILITY_OPTIONS,
            'sources' => SupportResource::SOURCE_OPTIONS,
        ]);
    }

    public function update(UpdateSupportResourceRequest $request, SupportResource $supportResource): RedirectResponse
    {
        $this->applyPayload($supportResource, $request);
        $supportResource->save();

        return redirect()
            ->route('admin.resources.index')
            ->with('status', 'Resource updated successfully.');
    }

    public function destroy(SupportResource $supportResource): RedirectResponse
    {
        if (! blank($supportResource->file_path)) {
            Storage::disk('local')->delete($supportResource->file_path);
        }

        $supportResource->delete();

        return redirect()
            ->route('admin.resources.index')
            ->with('status', 'Resource deleted successfully.');
    }

    private function applyPayload(
        SupportResource $supportResource,
        StoreSupportResourceRequest|UpdateSupportResourceRequest $request,
    ): void {
        $validated = $request->validated();

        $supportResource->fill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'resource_type' => $validated['resource_type'],
            'visibility' => $validated['visibility'],
            'source_type' => $validated['source_type'],
        ]);

        if ($validated['source_type'] === 'upload') {
            if ($request->hasFile('upload_file')) {
                if (! blank($supportResource->file_path)) {
                    Storage::disk('local')->delete($supportResource->file_path);
                }

                $uploadedFile = $request->file('upload_file');
                $storedPath = $uploadedFile->store('support-resources', 'local');

                $supportResource->file_path = $storedPath;
                $supportResource->file_name = $uploadedFile->getClientOriginalName();
                $supportResource->mime_type = $uploadedFile->getClientMimeType() ?: $uploadedFile->getMimeType();
                $supportResource->file_size = $uploadedFile->getSize();
            }

            if (blank($supportResource->file_path)) {
                throw ValidationException::withMessages([
                    'upload_file' => 'A local file is required when source type is upload.',
                ]);
            }

            $supportResource->link_url = null;

            return;
        }

        if (! blank($supportResource->file_path)) {
            Storage::disk('local')->delete($supportResource->file_path);
        }

        $supportResource->file_path = null;
        $supportResource->file_name = null;
        $supportResource->mime_type = null;
        $supportResource->file_size = null;
        $supportResource->link_url = $validated['link_url'];
    }
}
