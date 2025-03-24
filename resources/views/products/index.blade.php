@if(auth()->user()->role->name === 'Customer')
    <button onclick="buyProduct({{ $product->id }})">Buy</button>
@endif