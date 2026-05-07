<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create User') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                        <p class="font-semibold">{{ __('Please fix the errors below.') }}</p>
                        <ul class="mt-2 list-disc ps-5 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.user-roles.store') }}">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        @php
                            $selectedRoleIds = collect(old('roles', []))
                                ->map(fn ($id) => (int) $id)
                                ->all();
                        @endphp

                        <div>
                            <x-input-label :value="__('Assign Roles')" />
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
                                    <p class="text-sm text-gray-500">{{ __('No roles available.') }}</p>
                                @endforelse
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                            <x-input-error :messages="$errors->get('roles.*')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Create User') }}</x-primary-button>
                        <a href="{{ route('admin.user-roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
