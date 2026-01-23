@props([
    'title',
    'value',
    'description' => null,
    'icon',
    'trend' => null,
    'trendPositive' => true,
    'variant' => 'default'
])

@php
    $colors = match($variant) {
        'success' => [
            'bar' => 'bg-green-500',
            'icon' => 'text-green-500 dark:text-green-400',
        ],
        'primary' => [
            'bar' => 'bg-primary',
            'icon' => 'text-blue-500 dark:text-blue-400',
        ],
        default => [
            'bar' => 'bg-gray-300 dark:bg-gray-600',
            'icon' => 'text-gray-400 dark:text-gray-500',
        ]
    };

    $trendColor = $trendPositive 
        ? 'text-green-600 dark:text-green-400' 
        : 'text-red-600 dark:text-red-400';
        
    $trendIcon = $trendPositive ? '↑' : '↓';
@endphp


<x-mary-card class="!p-0 !gap-0 overflow-hidden transition-all duration-300 hover:shadow-lg border border-gray-200 dark:border-base-300 dark:bg-base-100">
    <div class="h-1.5 w-full {{ $colors['bar'] }}"></div>

    <div class="p-6">
        <div class="flex flex-row items-center justify-between pb-2">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $title }}
            </h3>
            
            {{-- Ícone Dinâmico --}}
            <x-mary-icon name="{{ $icon }}" class="h-5 w-5 {{ $colors['icon'] }}" />
        </div>

        {{-- Valor Principal --}}
        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ $value }}
        </div>

        {{-- Descrição --}}
        @if($description)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $description }}
            </p>
        @endif

        {{-- Tendência (Trend) --}}
        @if($trend)
            <div class="text-sm font-medium mt-2 {{ $trendColor }}">
                {{ $trendIcon }} {{ $trend }}
            </div>
        @endif
    </div>
</x-mary-card>