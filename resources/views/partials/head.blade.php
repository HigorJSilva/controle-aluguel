<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ $title ?? config('app.name', 'Laravel') }}
    @if (isset($title))
        - {{ config('app.name', 'Laravel') }}
    @endif
</title>
<meta name="description" content="{{ $metaDescription ?? config('app.description', 'Default description') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])
<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js"></script>
@stack('scripts')
