@php
    $currentResource = $supportResource ?? null;
    $selectedSource = old('source_type', $currentResource?->source_type ?? 'upload');
    $selectedRemovalIds = collect(old('remove_file_ids', []))
        ->map(fn ($id) => (int) $id)
        ->all();
    $existingFiles = $currentResource?->files ?? collect();
    $selectedTagIds = collect(old('tag_ids', $currentResource?->tags?->pluck('id')->all() ?? []))
        ->map(fn ($id) => (int) $id)
        ->all();
    $uploadFileErrors = array_merge($errors->get('upload_files'), $errors->get('upload_files.*'));
    $removeFileErrors = array_merge($errors->get('remove_file_ids'), $errors->get('remove_file_ids.*'));
    $allowedExtensions = config('uploads.allowed_extensions');
@endphp

<div class="space-y-5">
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $currentResource?->title ?? '')" required />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug')" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $currentResource?->slug ?? '')" />
        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea
            id="description"
            name="description"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
        >{{ old('description', $currentResource?->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="resource_type" :value="__('Type')" />
            <select id="resource_type" name="resource_type" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(old('resource_type', $currentResource?->resource_type ?? '') === $type)>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('resource_type')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="visibility" :value="__('Visibility')" />
            <select id="visibility" name="visibility" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach ($visibilities as $visibility)
                    <option value="{{ $visibility }}" @selected(old('visibility', $currentResource?->visibility ?? 'public') === $visibility)>
                        {{ ucfirst($visibility) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('visibility')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="source_type" :value="__('Source Type')" />
            <select id="source_type" name="source_type" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach ($sources as $source)
                    <option value="{{ $source }}" @selected($selectedSource === $source)>
                        {{ ucfirst($source) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('source_type')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $currentResource?->status ?? 'draft') === $status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="version" :value="__('Version')" />
            <x-text-input id="version" name="version" type="text" class="mt-1 block w-full" :value="old('version', $currentResource?->version ?? '')" />
            <x-input-error :messages="$errors->get('version')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="category_id" :value="__('Category')" />
            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('None') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $currentResource?->category_id) === $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="tag_ids" :value="__('Tags')" />
        <select id="tag_ids" name="tag_ids[]" multiple class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @foreach ($tags as $tag)
                <option value="{{ $tag->id }}" @selected(in_array($tag->id, $selectedTagIds, true))>{{ $tag->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('tag_ids')" class="mt-2" />
    </div>

    <div>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-indigo-600" @checked((bool) old('is_featured', $currentResource?->is_featured ?? false)) />
            <span>{{ __('Featured resource') }}</span>
        </label>
    </div>

    <div>
        <x-input-label for="changelog" :value="__('Changelog')" />
        <textarea id="changelog" name="changelog" rows="4" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('changelog', $currentResource?->changelog ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('changelog')" class="mt-2" />
    </div>

    <div id="upload_section" class="{{ $selectedSource === 'upload' ? '' : 'hidden' }}">
        <x-input-label for="upload_files" :value="__('Upload Files (local storage)')" />
        <input
            id="upload_files"
            name="upload_files[]"
            type="file"
            multiple
            class="mt-1 block w-full text-sm border-gray-300 rounded-md"
        />
        <p class="mt-1 text-xs text-gray-500">
            @if ($allowedExtensions === null)
                {{ __('All file types are allowed.') }}
            @else
                {{ __('Allowed file extensions:') }} {{ implode(', ', $allowedExtensions) }}
            @endif
        </p>
        <x-input-error :messages="$uploadFileErrors" class="mt-2" />

        @if ($currentResource !== null)
            <div class="mt-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                <p class="text-sm font-medium text-gray-800">{{ __('Current files') }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('Select files to remove when saving changes.') }}</p>

                <div class="mt-3 space-y-2">
                    @forelse ($existingFiles as $resourceFile)
                        <label class="flex items-center justify-between gap-3 p-2 bg-white border border-gray-200 rounded">
                            <span class="text-sm text-gray-800">
                                {{ $resourceFile->file_name }}
                                @if ($resourceFile->file_size)
                                    <span class="text-xs text-gray-500">
                                        ({{ number_format($resourceFile->file_size / 1024, 1) }} KB)
                                    </span>
                                @endif
                            </span>
                            <span class="inline-flex items-center gap-2 text-xs text-gray-600">
                                <span>{{ trans_choice(':count download|:count downloads', $resourceFile->download_count, ['count' => $resourceFile->download_count]) }}</span>
                                <input
                                    type="checkbox"
                                    name="remove_file_ids[]"
                                    value="{{ $resourceFile->id }}"
                                    class="rounded border-gray-300 text-red-600"
                                    @checked(in_array($resourceFile->id, $selectedRemovalIds, true))
                                />
                                <span>{{ __('Remove') }}</span>
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No files uploaded yet.') }}</p>
                    @endforelse
                </div>

                <x-input-error :messages="$removeFileErrors" class="mt-2" />
            </div>
        @endif
    </div>

    <div id="link_section" class="{{ $selectedSource === 'link' ? '' : 'hidden' }}">
        <x-input-label for="link_url" :value="__('Internal or External Link')" />
        <x-text-input
            id="link_url"
            name="link_url"
            type="text"
            class="mt-1 block w-full"
            :value="old('link_url', $currentResource?->link_url ?? '')"
            :placeholder="__('https://example.com/file or /internal/path')"
        />
        <x-input-error :messages="$errors->get('link_url')" class="mt-2" />
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sourceType = document.getElementById('source_type');
        const uploadSection = document.getElementById('upload_section');
        const linkSection = document.getElementById('link_section');

        const syncSections = () => {
            if (sourceType.value === 'upload') {
                uploadSection.classList.remove('hidden');
                linkSection.classList.add('hidden');
            } else {
                uploadSection.classList.add('hidden');
                linkSection.classList.remove('hidden');
            }
        };

        sourceType.addEventListener('change', syncSections);
        syncSections();
    });
</script>
