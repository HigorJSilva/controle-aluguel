<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ $title ?? config('app.name', 'Laravel') }}
    @if (isset($title))
    - {{ config('app.name', 'Laravel') }}
    @endif
</title>
<meta name="description" content="{{ $metaDescription ?? config('app.description', 'Default description') }}">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


@vite(['resources/css/app.css', 'resources/js/app.js'])
<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js"></script>
<script src="https://unpkg.com/flatpickr/dist/plugins/monthSelect/index.js"></script>
<link href="https://unpkg.com/flatpickr/dist/plugins/monthSelect/style.css" rel="stylesheet">
<script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>
@stack('scripts')