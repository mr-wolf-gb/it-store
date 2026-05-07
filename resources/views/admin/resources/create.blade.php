<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Resource
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                        <p class="font-semibold">Please fix the errors below.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.resources._form')

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Create Resource</x-primary-button>
                        <a href="{{ route('admin.resources.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
