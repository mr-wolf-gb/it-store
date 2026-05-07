<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Role
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.roles._form')

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('admin.roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
