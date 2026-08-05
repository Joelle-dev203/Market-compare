@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-xl mb-6">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div id="deleteModal" class="fixed inset-0 bg-gray-900/50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl max-w-sm mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Shop Permanently?</h3>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone. All your listings will be removed from PrixCameroon.</p>
            <div class="flex space-x-3">
                <button onclick="document.getElementById('deleteModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-100 rounded-xl text-sm font-bold">Cancel</button>
                <form action="{{ route('vendor.destroy') }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="permanently_delete" value="1">
                    <button type="submit" class="w-full px-4 py-2 bg-rose-600 text-white rounded-xl text-sm font-bold">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteProductModal" class="fixed inset-0 bg-gray-900/50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl max-w-sm mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Remove Product?</h3>
            <p class="text-sm text-gray-500 mb-6">This will remove the item from your shop offerings.</p>
            <div class="flex space-x-3">
                <button onclick="document.getElementById('deleteProductModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-100 rounded-xl text-sm font-bold">Cancel</button>
                <form id="deleteProductForm" action="" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-rose-600 text-white rounded-xl text-sm font-bold">Remove</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmProductDelete(url) {
            document.getElementById('deleteProductForm').action = url;
            document.getElementById('deleteProductModal').classList.remove('hidden');
        }
    </script>

    <header class="bg-teal-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('product.search') }}" class="text-xl font-bold tracking-tight text-white">
                    PriceCheck<span class="text-amber-400">Cameroon</span> Workspace
                </a>
                <span class="hidden md:inline-block text-xs bg-teal-900/60 text-teal-200 border border-teal-700/60 px-3 py-1 rounded-full font-medium">
                    <i class="fas fa-store mr-1.5 text-amber-400"></i> {{ $vendor->name ?? 'My Shop' }}
                </span>
            </div>
            <div class="flex items-center space-x-3">
                <form action="{{ route('vendor.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-teal-900/40 hover:bg-teal-700/80 text-teal-100 hover:text-white px-3 py-1.5 rounded-xl border border-teal-700/50 text-xs font-semibold transition">
                        Logout
                    </button>
                </form>

                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-rose-900/40 hover:bg-rose-600 text-rose-200 hover:text-white px-3 py-1.5 rounded-xl border border-rose-700/50 text-xs font-semibold transition">
                    Delete Shop
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Register your product</h2>
                <p class="text-xs text-gray-500 mb-6">Fill in the details below to add a new product to your shop's inventory.</p>

                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm p-4 rounded-xl mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('vendor.update_price') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Product Name</label>
                            <input type="text" name="new_product_name" required placeholder="e.g., iPhone 15 Pro, Dell latitude" class="w-full p-3 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Product Description</label>
                            <textarea name="description" rows="3" placeholder="Briefly describe the product features..." class="w-full p-3 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Category Type</label>
                            <select name="category" class="w-full p-3 border rounded-xl bg-gray-50 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                <optgroup label="Electronics">
                                    <option value="Computers">Computers</option>
                                    <option value="Phones">Phones</option>
                                    <option value="Cameras">Cameras</option>
                                </optgroup>
                                <optgroup label="Grocery">
                                    <option value="Fruits">Fruits</option>
                                    <option value="Vegetables">Vegetables</option>
                                    <option value="Beverages">Beverages</option>
                                </optgroup>
                                <optgroup label="Jewelries">
                                    <option value="Necklaces">Necklaces</option>
                                    <option value="Rings">Rings</option>
                                    <option value="Watches">Watches</option>
                                </optgroup>
                                <optgroup label="Clothing">
                                    <option value="Men">Men</option>
                                    <option value="Women">Women</option>
                                    <option value="Shoes">Shoes</option>
                                    <option value="Handbags">Handbags</option>
                                </optgroup>
                                <optgroup label="Home">
                                    <option value="Furniture">Furniture</option>
                                    <option value="Kitchen">Kitchen</option>
                                    <option value="Decor">Decor</option>
                                </optgroup>
                                <optgroup label="Health & Beauty">
                                    <option value="Skincare">Skincare</option>
                                    <option value="Makeup">Makeup</option>
                                    <option value="Fragrances">Fragrances</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Product Image</label>
                            <input type="file" name="product_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Your Price (CFA)</label>
                        <input type="number" name="price" required placeholder="Enter price" class="w-full p-3 border border-emerald-300 rounded-xl font-bold text-emerald-700 outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition text-sm uppercase tracking-wider shadow-sm">
                        Register Product
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Your Store Offerings</h2>
                <p class="text-xs text-gray-500 mb-6">These are the prices customers see when looking up items from your shop.</p>
                
                @if($vendor->products->isEmpty())
                    <div class="text-center py-12 border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                        <p class="text-gray-400 text-sm">Your store catalog is currently empty.</p>
                        <p class="text-xs text-gray-400 mt-1">Use the left panel to register your first product rates.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($vendor->products as $product)
                            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                                <div>
                                    <div class="w-full h-40 bg-gray-100 rounded-xl overflow-hidden mb-3 border border-gray-50">
                                        @if(isset($product->image_url) && $product->image_url)
                                            @php
                                                $cleanPath = str_replace(['storage/app/public/', 'public/'], '', $product->image_url);
                                            @endphp
                                            <img src="{{ asset($cleanPath) }}" alt="Product Image" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="fas fa-box text-2xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 bg-teal-50 text-teal-700 rounded-md">
                                            {{ $product->category ?? 'General' }}
                                        </span>
                                        <span class="text-lg font-extrabold text-teal-600">
                                            <a href="{{ route('vendor.edit_price', $product->id) }}" class="hover:underline">
                                                {{ number_format($product->pivot->price) }} CFA
                                            </a>
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-base mb-1">{{ $product->name }}</h3>
                                    <p class="text-xs text-gray-500 line-clamp-2 mb-4">
                                        {{ $product->description ?? 'No description provided for this product.' }}
                                    </p>
                                </div>
                                <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-xs font-semibold">
                                    <a href="{{ route('vendor.price_history', $product->id) }}" class="text-emerald-600 hover:text-emerald-800">
                                        <i class="fas fa-history mr-1"></i> View History
                                    </a>
                                    <button onclick="confirmProductDelete('{{ route('vendor.remove_product', $product->id) }}')" class="text-rose-500 hover:text-rose-700 uppercase tracking-wider font-bold text-[10px]">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>