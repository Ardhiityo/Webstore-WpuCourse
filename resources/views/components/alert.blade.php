@props([
    'type' => session()->has('error') ? 'error' : (session()->has('success') ? 'success' : 'info'),
    'message' => session('error') ?? (session('success') ?? session('info')),
])

@if ($message || $errors->any())
    <div
        class="p-4 text-center
        @if ($type === 'error') bg-red-500 text-white
        @elseif($type === 'success') bg-green-500
        @else bg-yellow-300 @endif">

        @if ($message)
            {{ $message }}
        @endif
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
