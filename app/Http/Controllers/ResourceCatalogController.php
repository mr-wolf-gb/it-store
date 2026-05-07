<?php

namespace App\Http\Controllers;

use App\Models\SupportResource;
use App\Models\SupportResourceFile;
use App\Models\ResourceTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResourceCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $canViewPrivate = $request->user()?->can('resources.view_private')
            || $request->user()?->can('resources.download_private')
            || $request->user()?->can('resources.manage');

        $search = $request->string('q')->toString();
        $resourceType = $request->string('resource_type')->toString();
        $visibility = $request->string('visibility')->toString();
        $categoryId = $request->integer('category_id');
        $tagId = $request->integer('tag_id');

        $resources = SupportResource::query()
            ->with(['uploader', 'files', 'category', 'tags'])
            ->where('status', 'published')
            ->when(! $canViewPrivate, fn ($query) => $query->where('visibility', 'public'))
            ->when(
                $canViewPrivate && in_array($visibility, SupportResource::VISIBILITY_OPTIONS, true),
                fn ($query) => $query->where('visibility', $visibility)
            )
            ->when(
                in_array($resourceType, SupportResource::TYPE_OPTIONS, true),
                fn ($query) => $query->where('resource_type', $resourceType)
            )
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->when($tagId > 0, function ($query) use ($tagId): void {
                $query->whereHas('tags', fn ($tagQuery) => $tagQuery->where('resource_tags.id', $tagId));
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('library.index', [
            'resources' => $resources,
            'canViewPrivate' => $canViewPrivate,
            'types' => SupportResource::TYPE_OPTIONS,
            'categories' => \App\Models\ResourceCategory::query()->orderBy('name')->get(),
            'tags' => ResourceTag::query()->orderBy('name')->get(),
            'latestResources' => SupportResource::query()
                ->where('status', 'published')
                ->when(! $canViewPrivate, fn ($query) => $query->where('visibility', 'public'))
                ->latest('updated_at')
                ->limit(5)
                ->get(),
            'mostDownloadedResources' => SupportResource::query()
                ->where('status', 'published')
                ->when(! $canViewPrivate, fn ($query) => $query->where('visibility', 'public'))
                ->orderByDesc('download_count')
                ->limit(5)
                ->get(),
            'filters' => [
                'q' => $search,
                'resource_type' => $resourceType,
                'visibility' => $visibility,
                'category_id' => $categoryId > 0 ? (string) $categoryId : '',
                'tag_id' => $tagId > 0 ? (string) $tagId : '',
            ],
        ]);
    }

    public function download(SupportResource $supportResource): RedirectResponse|StreamedResponse
    {
        Gate::authorize('download', $supportResource);

        if ($supportResource->source_type === 'link') {
            abort_if(blank($supportResource->link_url), 404);

            $this->incrementDownloadCounters($supportResource);

            return Str::startsWith($supportResource->link_url, ['http://', 'https://'])
                ? redirect()->away($supportResource->link_url)
                : redirect($supportResource->link_url);
        }

        $resourceFile = $supportResource->files()->oldest('id')->first();

        if ($resourceFile !== null) {
            return $this->downloadStoredFile(
                $supportResource,
                $resourceFile->file_path,
                $resourceFile->file_name,
                $resourceFile,
            );
        }

        abort_if(blank($supportResource->file_path), 404);

        return $this->downloadStoredFile(
            $supportResource,
            $supportResource->file_path,
            $supportResource->file_name,
        );
    }

    public function downloadFile(
        SupportResource $supportResource,
        SupportResourceFile $resourceFile,
    ): StreamedResponse {
        Gate::authorize('download', $supportResource);
        abort_if($resourceFile->support_resource_id !== $supportResource->id, 404);

        return $this->downloadStoredFile(
            $supportResource,
            $resourceFile->file_path,
            $resourceFile->file_name,
            $resourceFile,
        );
    }

    public function downloadAllFiles(SupportResource $supportResource): BinaryFileResponse
    {
        Gate::authorize('download', $supportResource);
        abort_if($supportResource->source_type !== 'upload', 404);

        $files = $supportResource->files()->orderBy('id')->get();
        abort_if($files->isEmpty(), 404);

        $disk = Storage::disk('local');
        $zipPath = tempnam(sys_get_temp_dir(), 'resource-');
        if ($zipPath === false) {
            abort(500, 'Unable to prepare archive file.');
        }

        $zipPathWithExt = $zipPath.'.zip';
        @rename($zipPath, $zipPathWithExt);

        $zip = new ZipArchive;
        if ($zip->open($zipPathWithExt, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Unable to create zip archive.');
        }

        $addedEntries = 0;
        $usedNames = [];

        foreach ($files as $file) {
            if (! $disk->exists($file->file_path)) {
                continue;
            }

            $absolutePath = $disk->path($file->file_path);
            $entryName = $file->file_name ?: basename($file->file_path);
            $entryName = $this->uniqueEntryName($entryName, $usedNames);
            $zip->addFile($absolutePath, $entryName);
            $addedEntries++;
        }

        $zip->close();
        abort_if($addedEntries === 0, 404);

        $this->incrementDownloadCounters($supportResource);
        $supportResource->files()->increment('download_count');

        $archiveName = Str::slug($supportResource->title ?: 'resource-files').'-files.zip';

        return response()->download($zipPathWithExt, $archiveName)->deleteFileAfterSend(true);
    }

    /**
     * @param array<string, bool> $usedNames
     */
    private function uniqueEntryName(string $entryName, array &$usedNames): string
    {
        if (! isset($usedNames[$entryName])) {
            $usedNames[$entryName] = true;

            return $entryName;
        }

        $dotPos = strrpos($entryName, '.');
        $baseName = $dotPos === false ? $entryName : substr($entryName, 0, $dotPos);
        $extension = $dotPos === false ? '' : substr($entryName, $dotPos);

        $suffix = 1;
        do {
            $candidate = $baseName.'-'.$suffix.$extension;
            $suffix++;
        } while (isset($usedNames[$candidate]));

        $usedNames[$candidate] = true;

        return $candidate;
    }

    private function downloadStoredFile(
        SupportResource $supportResource,
        string $filePath,
        ?string $fileName = null,
        ?SupportResourceFile $resourceFile = null,
    ): StreamedResponse {
        $disk = Storage::disk('local');
        abort_if(! $disk->exists($filePath), 404);

        $this->incrementDownloadCounters($supportResource, $resourceFile);

        return $disk->download(
            $filePath,
            $fileName ?: basename($filePath),
        );
    }

    private function incrementDownloadCounters(SupportResource $supportResource, ?SupportResourceFile $resourceFile = null): void
    {
        $key = 'resource_downloaded_'.$supportResource->id;
        if (! session()->has($key)) {
            $supportResource->increment('download_count');
            session()->put($key, now()->addMinutes(10)->timestamp);
        }

        if ($resourceFile !== null) {
            $fileKey = 'resource_file_downloaded_'.$resourceFile->id;
            if (! session()->has($fileKey)) {
                $resourceFile->increment('download_count');
                session()->put($fileKey, now()->addMinutes(10)->timestamp);
            }
        }
    }
}