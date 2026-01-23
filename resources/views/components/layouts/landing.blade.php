@php
    $layout = config('app.appearance.landing_layout');
@endphp

<x-dynamic-component :component="'layouts.app.' . $layout" :title="$pageTitle ?? null">
    {{ $slot }}
</x-dynamic-component>
