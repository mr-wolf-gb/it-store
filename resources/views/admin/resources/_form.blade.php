@php
    $selectedSource = old('source_type', $supportResource->source_type ?? 'upload');
@endphp

<div class="space-y-5">
    <div>
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $supportResource->title ?? '')" required />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea
            id="description"
            name="description"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
        >{{ old('description', $supportResource->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="resource_type" value="Type" />
            <select id="resource_type" name="resource_type" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(old('resource_type', $supportResource->resource_type ?? '') === $type)>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('resource_type')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="visibility" value="Visibility" />
            <select id="visibility" name="visibility" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach ($visibilities as $visibility)
                    <option value="{{ $visibility }}" @selected(old('visibility', $supportResource->visibility ?? 'public') === $visibility)>
                        {{ ucfirst($visibility) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('visibility')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="source_type" value="Source Type" />
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

    <div id="upload_section" class="{{ $selectedSource === 'upload' ? '' : 'hidden' }}">
        <x-input-label for="upload_file" value="Upload File (local storage)" />
        <input
            id="upload_file"
            name="upload_file"
            type="file"
            class="mt-1 block w-full text-sm border-gray-300 rounded-md"
            @if (! isset($supportResource)) required @endif
        />
        <x-input-error :messages="$errors->get('upload_file')" class="mt-2" />

        @if (isset($supportResource) && ! blank($supportResource->file_name))
            <p class="mt-2 text-xs text-gray-500">Current file: {{ $supportResource->file_name }}</p>
        @endif
    </div>

    <div id="link_section" class="{{ $selectedSource === 'link' ? '' : 'hidden' }}">
        <x-input-label for="link_url" value="Internal or External Link" />
        <x-text-input
            id="link_url"
            name="link_url"
            type="text"
            class="mt-1 block w-full"
            :value="old('link_url', $supportResource->link_url ?? '')"
            placeholder="https://example.com/file or /internal/path"
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
