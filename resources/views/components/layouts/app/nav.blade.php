<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen font-sans antialiased bg-base-300">
    <x-mary-nav sticky class="bg-base-200/90">
        <x-slot:brand>
            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse"
                wire:navigate>
                <x-app-logo />
            </a>
        </x-slot:brand>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                @if(empty(Auth::user()))
                <x-mary-button label="login" :link="route('login')" />
                <x-mary-button label="register" :link="route('register')" />
                @else
                <x-mary-button label="Dashboard" link="{{ route('dashboard') }}" class="" />
                @endif
                <x-mary-theme-toggle />
            </div>
        </x-slot:actions>
    </x-mary-nav>

    <x-mary-main full-width>

        <x-slot:content class="flex flex-col min-h-screen !p-0">
            <div class="flex-1 flex flex-col items-stretch gap-2">
                {{ $slot }}
            </div>
            <x-partials.footer-info />
        </x-slot:content>
    </x-mary-main>

</body>

</html>