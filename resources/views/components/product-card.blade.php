{{-- resources/views/components/product-card.blade.php --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-lg transition flex flex-col">
    <img src="{{ $product->image_url ? asset($product->image_url) : 'https://via.placeholder.com/200' }}" 
         class="h-40 object-contain mb-4" alt="{{ $product->name }}">
    
    <h3 class="font-bold text-gray-800 mb-1">{{ $product->name }}</h3>
    
    <p class="text-emerald-600 font-black text-lg mb-4">
        {{ number_format($product->vendors->first()->pivot->price ?? 0) }} CFA
    </p>
    
    <a href="{{ route('product.show', $product->id) }}" 
       class="mt-auto w-full text-center py-2 bg-gray-100 rounded-lg text-sm font-bold hover:bg-emerald-600 hover:text-white transition">
       View Details
    </a>
</div>