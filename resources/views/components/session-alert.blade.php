@if ($successMessage)
    <div {{ $attributes->merge(['class' => 'mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md dark:bg-green-700 dark:text-green-100 dark:border-green-600']) }}>
        {{ $successMessage }}
    </div>
@endif

@if ($errorMessage)
    <div {{ $attributes->merge(['class' => 'mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md dark:bg-red-700 dark:text-red-100 dark:border-red-600']) }}>
        {{ $errorMessage }}
    </div>
@endif
