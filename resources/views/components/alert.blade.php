@props([
    'type' => session('success') ? 'success' : (session('error') ? 'error' : 'info'),
    'message' => session('success') ?? (session('error') ?? session('info')),
])

@if ($message || $errors->any())
    <div
        class="py-4 text-center @if ($type === 'success') bg-green-500
    @elseif ($type === 'error') bg-red-500 text-white
    @else bg-yellow-200 @endif">

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if ($message)
            <span>{{ $message }}</span>
        @endif

    </div>
@endif
