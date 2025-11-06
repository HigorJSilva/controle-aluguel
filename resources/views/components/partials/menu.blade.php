<x-mary-menu {{ $attributes }} activate-by-route>
    <x-mary-menu-item title="Dashboard" icon="m-rectangle-group" :link="route('dashboard')" />
    <x-mary-menu-item title="Imoveis" icon="m-building-office-2" :link="route('imoveis.create')" />
    {{-- <x-mary-menu-item title="Users" icon="s-users" :link="route('users.index')" /> --}}
</x-mary-menu>
