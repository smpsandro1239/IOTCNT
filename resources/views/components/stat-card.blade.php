@props(['title', 'value', 'subtitle' => '', 'icon', 'color' => 'blue', 'id' => ''])

@php
    $colorClasses = [
        'green' => 'stat-card-green text-green-600 dark:text-green-400',
        'blue' => 'stat-card-blue text-blue-600 dark:text-blue-400',
        'purple' => 'stat-card-purple text-purple-600 dark:text-purple-400',
        'red' => 'stat-card-red text-red-600 dark:text-red-400',
        'yellow' => 'stat-card-yellow text-yellow-600 dark:text-yellow-400'
    ];

    $iconSvgs = [
        'valves' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>',
        'operations' => '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        'time' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>',
        'status' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'
    ];
@endphp

<div class="stat-card {{ $colorClasses[$color] ?? $colorClasses['blue'] }}" {{ $id ? "id=$id" : '' }}>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ $title }}</h3>
            <p class="text-3xl font-bold {{ explode(' ', $colorClasses[$color] ?? $colorClasses['blue'])[1] }}" {{ $id ? "id=$id-value" : '' }}>
                @if($value === null || $value === '')
                    <span class="loading-spinner"></span>
                @else
                    {{ $value }}
                @endif
            </p>
            @if($subtitle)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="text-4xl">
            <svg class="w-12 h-12 {{ explode(' ', $colorClasses[$color] ?? $colorClasses['blue'])[1] }}" fill="currentColor" viewBox="0 0 20 20">
                {!! $iconSvgs[$icon] ?? $iconSvgs['status'] !!}
            </svg>
        </div>
    </div>
</div>
