<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Roles for {{ $managedUser->name }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="POST" action="{{ route('admin.user-roles.update', $managedUser) }}">
                    @csrf
                    @method('PUT')

                    @php
                        $selectedRoleIds = collect(old('roles', $managedUser->roles->pluck('id')->all()))
                            ->map(fn ($id) => (int) $id)
                            ->all();
                    @endphp

                    <div>
                        <x-input-label value="Assign Roles" />
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 p-3 border border-gray-200 rounded-md">
                            @forelse ($roles as $role)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $role->id }}"
                                        class="rounded border-gray-300 text-indigo-600"
                                        @checked(in_array($role->id, $selectedRoleIds, true))
                                    />
                                    <span>{{ $role->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">No roles available.</p>
                            @endforelse
                        </div>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        <x-input-error :messages="$errors->get('roles.*')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Save Roles</x-primary-button>
                        <a href="{{ route('admin.user-roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
