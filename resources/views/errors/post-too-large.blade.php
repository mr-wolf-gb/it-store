<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Upload too large') }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
        <div class="max-w-xl w-full bg-white border border-red-200 rounded-lg p-6">
            <h1 class="text-xl font-semibold text-red-700">{{ __('Content too large') }}</h1>
            <p class="mt-3 text-sm text-gray-700">{{ $message }}</p>
            <div class="mt-5 text-xs text-gray-600">
                <p>{{ __('Current request exceeded server POST limits.') }}</p>
                <p>{{ __('Try uploading smaller files, fewer files, or increase PHP upload limits.') }}</p>
            </div>
            <div class="mt-6">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm rounded-md hover:bg-black">
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </body>
</html>
