@props(['title', 'value', 'iconPath', 'iconClass' => 'text-gray-500'])

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
    <div class="flex items-center">
        <div class="p-3 rounded-full bg-opacity-20 {{
            Str::contains($iconClass, 'blue') ? 'bg-blue-100 dark:bg-blue-900' :
            (Str::contains($iconClass, 'green') ? 'bg-green-100 dark:bg-green-900' :
            (Str::contains($iconClass, 'purple') ? 'bg-purple-100 dark:bg-purple-900' :
            'bg-gray-100 dark:bg-gray-700'))
        }}">
            <svg class="h-8 w-8 {{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $title }}</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
        </div>
    </div>
</div>
