<x-mary-menu {{ $attributes }} activate-by-route>
    {{-- <x-mary-menu-item title="Users" icon="s-users" :link="route('users.index')" /> --}}
    <x-mary-menu-item title="Dashboard" icon="m-rectangle-group" :link="route('dashboard')" wire:navigate/>
    <x-mary-menu-item title="Imoveis" icon="m-building-office-2" :link="route('imoveis.index')" wire:navigate/>
</x-mary-menu>
