@php
    $selectedPermissions = collect(old('permissions', isset($role) ? $role->permissions->pluck('id')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

<div class="space-y-5">
    <div>
        <x-input-label for="name" value="Role Name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $role->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="Permissions" />
        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 p-3 border border-gray-200 rounded-md max-h-72 overflow-auto">
            @forelse ($permissions as $permission)
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->id }}"
                        class="rounded border-gray-300 text-indigo-600"
                        @checked(in_array($permission->id, $selectedPermissions, true))
                    />
                    <span>{{ $permission->name }}</span>
                </label>
            @empty
                <p class="text-sm text-gray-500">No permissions available.</p>
            @endforelse
        </div>
        <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
        <x-input-error :messages="$errors->get('permissions.*')" class="mt-2" />
    </div>
</div>
