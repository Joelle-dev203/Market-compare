<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit Price - PrixCameroon</title>
</head>
<body class="bg-gray-50 py-10">
    <div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Update Price</h2>
        
        <form action="{{ route('vendor.update_existing_price', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-500 uppercase font-bold">Product</p>
                <p class="font-bold text-gray-800">{{ $product->name }}</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">New Retail Price (CFA)</label>
                {{-- Use null-safe check to prevent "Attempt to read property on null" --}}
                <input type="number" name="price" 
                    value="{{ $product->pivot->price ?? 0 }}" 
                    required 
                    class="w-full p-3 border border-emerald-300 rounded-xl font-bold text-emerald-700 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('vendor.dashboard') }}" class="flex-1 text-center py-3 bg-gray-100 rounded-xl font-bold text-sm">Cancel</a>
                <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>