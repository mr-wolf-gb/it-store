@props(['messages'])

@if ($messages)
    @php
        $flattened = [];
        foreach ((array) $messages as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $msg) {
                    $flattened[] = $msg;
                }
            } else {
                $flattened[] = $value;
            }
        }
    @endphp
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ($flattened as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
