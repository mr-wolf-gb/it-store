<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportResourceRequest;
use App\Http\Requests\UpdateSupportResourceRequest;
use App\Models\ResourceCategory;
use App\Models\ResourceTag;
use App\Models\SupportResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SupportResourceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();
        $resourceType = $request->string('resource_type')->toString();
        $visibility = $request->string('visibility')->toString();
        $status = $request->string('status')->toString();
        $categoryId = $request->integer('category_id');

        $resources = SupportResource::query()
            ->with(['uploader', 'files', 'category', 'tags'])
            ->withCount('files')
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
            ->when(
                in_array($status, SupportResource::STATUS_OPTIONS, true),
                fn ($query) => $query->where('status', $status)
            )
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.resources.index', [
            'resources' => $resources,
            'types' => SupportResource::TYPE_OPTIONS,
            'statuses' => SupportResource::STATUS_OPTIONS,
            'categories' => ResourceCategory::query()->orderBy('name')->get(),
            'filters' => [
                'q' => $search,
                'resource_type' => $resourceType,
                'visibility' => $visibility,
                'status' => $status,
                'category_id' => $categoryId > 0 ? (string) $categoryId : '',
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.resources.create', [
            'types' => SupportResource::TYPE_OPTIONS,
            'visibilities' => SupportResource::VISIBILITY_OPTIONS,
            'sources' => SupportResource::SOURCE_OPTIONS,
            'statuses' => SupportResource::STATUS_OPTIONS,
            'categories' => ResourceCategory::query()->orderBy('name')->get(),
            'tags' => ResourceTag::query()->orderBy('name')->get(),
            'supportResource' => null,
        ]);
    }

    public function store(StoreSupportResourceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated): void {
            $supportResource = new SupportResource;
            $supportResource->user_id = $request->user()->id;
            $supportResource->created_by = $request->user()->id;
            $supportResource->updated_by = $request->user()->id;

            $this->fillBasePayload($supportResource, $validated);
            $supportResource->save();
            $supportResource->tags()->sync($validated['tag_ids'] ?? []);

            $this->syncSource($supportResource, $request, $validated);
        });

        return redirect()
            ->route('admin.resources.index')
            ->with('status', __('Resource created successfully.'));
    }

    public function edit(SupportResource $supportResource): View
    {
        return view('admin.resources.edit', [
            'supportResource' => $supportResource->load(['files', 'tags']),
            'types' => SupportResource::TYPE_OPTIONS,
            'visibilities' => SupportResource::VISIBILITY_OPTIONS,
            'sources' => SupportResource::SOURCE_OPTIONS,
            'statuses' => SupportResource::STATUS_OPTIONS,
            'categories' => ResourceCategory::query()->orderBy('name')->get(),
            'tags' => ResourceTag::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateSupportResourceRequest $request, SupportResource $supportResource): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($supportResource, $request, $validated): void {
            $supportResource->updated_by = $request->user()?->id;
            $this->fillBasePayload($supportResource, $validated);
            $supportResource->save();
            $supportResource->tags()->sync($validated['tag_ids'] ?? []);

            $this->syncSource($supportResource, $request, $validated);
        });

        return redirect()
            ->route('admin.resources.index')
            ->with('status', __('Resource updated successfully.'));
    }

    public function destroy(SupportResource $supportResource): RedirectResponse
    {
        DB::transaction(function () use ($supportResource): void {
            $this->deleteAllFiles($supportResource);
            $supportResource->delete();
        });

        return redirect()
            ->route('admin.resources.index')
            ->with('status', __('Resource deleted successfully.'));
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function fillBasePayload(
        SupportResource $supportResource,
        array $validated,
    ): void {
        $supportResource->fill([
            'title' => $validated['title'],
            'slug' => $this->resolveSlug($validated['slug'] ?? null, $validated['title'], $supportResource),
            'description' => $validated['description'] ?? null,
            'resource_type' => $validated['resource_type'],
            'visibility' => $validated['visibility'],
            'status' => $validated['status'],
            'version' => $validated['version'] ?? null,
            'changelog' => $validated['changelog'] ?? null,
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'category_id' => $validated['category_id'] ?? null,
            'source_type' => $validated['source_type'],
        ]);

        $supportResource->published_at = $validated['status'] === 'published'
            ? ($supportResource->published_at ?? now())
            : null;
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function syncSource(
        SupportResource $supportResource,
        StoreSupportResourceRequest|UpdateSupportResourceRequest $request,
        array $validated,
    ): void {
        if ($validated['source_type'] === 'upload') {
            $this->syncUploadedFiles($supportResource, $request, $validated);
            $supportResource->link_url = null;
            $this->clearLegacyFileColumns($supportResource);
            $supportResource->save();

            return;
        }

        $this->deleteAllFiles($supportResource);
        $supportResource->link_url = $validated['link_url'];
        $this->clearLegacyFileColumns($supportResource);
        $supportResource->save();
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function syncUploadedFiles(
        SupportResource $supportResource,
        StoreSupportResourceRequest|UpdateSupportResourceRequest $request,
        array $validated,
    ): void {
        $this->migrateLegacyFileIfNeeded($supportResource);

        $removeFileIds = collect($validated['remove_file_ids'] ?? [])
            ->map(static fn ($id): int => (int) $id)
            ->all();

        if ($removeFileIds !== []) {
            $filesToRemove = $supportResource->files()
                ->whereIn('id', $removeFileIds)
                ->get();

            foreach ($filesToRemove as $fileToRemove) {
                Storage::disk('local')->delete($fileToRemove->file_path);
            }

            if ($filesToRemove->isNotEmpty()) {
                $supportResource->files()
                    ->whereIn('id', $filesToRemove->pluck('id'))
                    ->delete();
            }
        }

        if ($request->hasFile('upload_files')) {
            foreach ((array) $request->file('upload_files') as $uploadedFile) {
                if ($uploadedFile === null) {
                    continue;
                }

                $storedPath = $uploadedFile->store('support-resources', 'local');

                $supportResource->files()->create([
                    'file_path' => $storedPath,
                    'file_name' => $uploadedFile->getClientOriginalName(),
                    'stored_name' => basename($storedPath),
                    'mime_type' => $uploadedFile->getClientMimeType() ?: $uploadedFile->getMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                    'uploaded_by' => $request->user()?->id,
                ]);
            }
        }

        if ($supportResource->files()->count() === 0) {
            throw ValidationException::withMessages([
                'upload_files' => __('At least one file is required when source type is upload.'),
            ]);
        }
    }

    private function deleteAllFiles(SupportResource $supportResource): void
    {
        $supportResource->loadMissing('files');

        foreach ($supportResource->files as $resourceFile) {
            Storage::disk('local')->delete($resourceFile->file_path);
        }

        $supportResource->files()->delete();

        if (! blank($supportResource->file_path)) {
            Storage::disk('local')->delete($supportResource->file_path);
        }
    }

    private function clearLegacyFileColumns(SupportResource $supportResource): void
    {
        $supportResource->file_path = null;
        $supportResource->file_name = null;
        $supportResource->mime_type = null;
        $supportResource->file_size = null;
    }

    private function migrateLegacyFileIfNeeded(SupportResource $supportResource): void
    {
        if (blank($supportResource->file_path)) {
            return;
        }

        $alreadyExists = $supportResource->files()
            ->where('file_path', $supportResource->file_path)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $supportResource->files()->create([
            'file_path' => $supportResource->file_path,
            'file_name' => $supportResource->file_name ?: basename($supportResource->file_path),
            'stored_name' => basename($supportResource->file_path),
            'mime_type' => $supportResource->mime_type,
            'file_size' => $supportResource->file_size,
            'uploaded_by' => $supportResource->updated_by ?: $supportResource->user_id,
        ]);
    }

    private function resolveSlug(?string $requestedSlug, string $title, SupportResource $supportResource): string
    {
        $baseSlug = Str::slug($requestedSlug ?: $title);
        if ($baseSlug === '') {
            $baseSlug = 'resource';
        }

        $slug = $baseSlug;
        $suffix = 1;

        while (
            SupportResource::query()
                ->where('slug', $slug)
                ->when($supportResource->exists, fn ($query) => $query->where('id', '!=', $supportResource->id))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}