@props([
    'type' => session()->has('success') ? 'success' : (session()->has('error') ? 'error' : 'info'),
    'message' => session()->get('success') ?? (session()->get('error') ?? (session()->get('info') ?? '')),
])

@if ($message)
    <div class="text-sm text-white text-center p-4
@if ($type === 'success') bg-green-600 @elseif ($type === 'error') bg-red-600 @elseif ($type === 'info') bg-blue-600 @endif"
        role="alert" tabindex="-1" aria-labelledby="hs-solid-color-dark-label">
        {{ $message }}
    </div>
@endif
