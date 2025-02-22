@php
    function isPrime($number) {
        if ($number < 2) return false;
        for ($i = 2; $i <= sqrt($number); $i++) {
            if ($number % $i == 0) return false;
        }
        return true;
    }
@endphp

<div class="card">
    <div class="card-header">Prime Numbers</div>
    <div class="card-body">
        @foreach (range(1, 100) as $i)
            @if (isPrime($i))
                <span class="badge bg-primary">{{ $i }}</span>
            @else
                <span class="badge bg-secondary">{{ $i }}</span>
            @endif
        @endforeach
    </div>
</div>