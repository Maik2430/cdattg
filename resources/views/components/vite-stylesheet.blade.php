@props(['paths'])

@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Vite;

    $entries = Arr::wrap($paths);
@endphp

@if (file_exists(public_path('hot')))
    @vite($entries)
@else
    @foreach ($entries as $entry)
        <link rel="stylesheet" href="{{ Vite::asset($entry) }}">
    @endforeach
@endif
