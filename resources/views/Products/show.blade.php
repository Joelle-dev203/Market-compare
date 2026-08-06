<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} | Compare Prices Cameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .vendor-card {
            transition: all 0.3s ease;
        }
        .vendor-card:hover {
            border-color: #059669 !important;
            box-shadow: 0 10px 30px -10px rgba(5, 150, 105, 0.15);
        }
        .toast-animate {
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .rating-star {
            transition: all 0.2s ease;
        }
        .rating-star:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ mobileMenu: false, categoryOpen: false }">

    <!-- ===== HEADER (STICKY) ===== -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('product.search') }}" class="text-2xl font-bold text-emerald-600 tracking-tight">
            PriceCheck<span class="text-amber-500">Cameroon</span>
        </a>
        
        <div class="hidden md:flex items-center space-x-6">
            <!-- 1. WISHLIST: Only shows when logged in -->
            @auth
                <a href="{{ route('wishlist.index') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">
                    <i class="fas fa-heart mr-1"></i> My Wishlist
                </a>
            @endauth

            <!-- 2. USER ACTIONS (Login/Logout/Register) -->
            @auth
                <span class="text-sm font-semibold text-gray-700">Hi, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-red-500 hover:text-red-700 transition">Logout</button>
                </form>
            @else
                <a href="{{ route('register') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">
                    <i class="fas fa-heart mr-1"></i> My Wishlist
                </a>
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">Login</a>
            @endauth

            <!-- 3. VENDOR & ABOUT -->
            @if(Auth::guard('vendor')->check())
                <a href="{{ route('vendor.dashboard') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-800 transition">Dashboard</a>
            @else
                <a href="{{ route('vendor.register.form') }}" class="flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                    <i class="fas fa-store mr-2"></i>Register Business
                </a>
            @endif
            
            <a href="{{ route('about') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">About</a>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-bars text-gray-700 text-xl"></i>
        </button>
    </div>

    <!-- Mobile Menu - Same as Desktop -->
    <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-t border-gray-100 py-4 px-4">
        <div class="flex flex-col space-y-3">
            <!-- Home -->
            <a href="{{ route('product.search') }}" class="text-gray-700 hover:text-emerald-600 font-medium">Home</a>
            
            <!-- Wishlist -->
            @auth
                <a href="{{ route('wishlist.index') }}" class="text-gray-700 hover:text-emerald-600 font-medium">
                    <i class="fas fa-heart mr-2 text-red-500"></i> My Wishlist
                </a>
            @else
                <a href="{{ route('register') }}" class="text-gray-700 hover:text-emerald-600 font-medium">
                    <i class="fas fa-heart mr-2"></i> My Wishlist
                </a>
            @endauth

            <!-- Auth Links -->
            @auth
                <span class="text-sm font-semibold text-gray-700">Hi, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-500 font-medium text-left hover:text-red-700 transition">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 font-medium">Login</a>
            @endauth

            <!-- Vendor -->
            @if(Auth::guard('vendor')->check())
                <a href="{{ route('vendor.dashboard') }}" class="text-emerald-600 font-medium">Dashboard</a>
            @else
                <a href="{{ route('vendor.register.form') }}" class="flex items-center text-emerald-600 font-medium">
                    <i class="fas fa-store mr-2"></i>Register Business
                </a>
            @endif
            
            <!-- About -->
            <a href="{{ route('about') }}" class="text-gray-700 hover:text-emerald-600 font-medium">About</a>
        </div>
    </div>
</header>

    <!-- ===== CATEGORY NAV (STICKY) ===== -->
    <nav class="bg-gray-900 text-white py-3 shadow-md sticky top-16 z-40" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center space-x-8">
            <button @click="open = !open" class="flex items-center text-sm font-bold uppercase tracking-wider hover:text-amber-400 transition">
                <i class="fas fa-bars mr-2"></i> All Categories <i class="fas fa-chevron-down ml-2 text-[10px]"></i>
            </button>
            <a href="{{ route('product.search') }}" class="text-sm font-bold hover:text-amber-400 transition">All Items</a>
        </div>

        <!-- Mega Menu Overlay -->
        <div x-show="open" @click.away="open = false" class="absolute left-0 w-full bg-white text-gray-800 shadow-2xl py-8 z-50 border-t border-gray-100" x-cloak>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-3 gap-8">
                @php
                    $menu = [
                        'Electronics' => ['Computers', 'Phones', 'Cameras'],
                        'Grocery' => ['Fruits', 'Vegetables', 'Beverages'],
                        'Jewelries' => ['Necklaces', 'Rings', 'Watches'],
                        'Clothing' => ['Men', 'Women', 'Shoes','Handbags'],
                        'Home' => ['Furniture', 'Kitchen', 'Decor'],
                        'Health & Beauty' => ['Skincare', 'Makeup', 'Fragrances']
                    ];
                @endphp

                @foreach($menu as $category => $items)
                    <div class="mb-4">
                        <p class="text-sm font-black text-emerald-600 uppercase tracking-widest mb-3 border-b pb-2">{{ $category }}</p>
                        <div class="space-y-1">
                            @foreach($items as $item)
                                <a href="{{ route('product.search', ['category' => $item]) }}" 
                                   class="block text-sm text-gray-600 hover:text-emerald-600 hover:underline">
                                   {{ $item }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="max-w-4xl mx-auto px-4 py-10">
        <a href="{{ route('product.search') }}" class="text-sm text-emerald-600 font-bold mb-6 inline-block hover:underline">← Back to Search</a>
        
        {{-- Header Section --}}
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row items-center gap-8 relative">
            @auth
                <div class="absolute top-6 right-6 flex flex-col gap-3">
                    {{-- Wishlist Button --}}
                    <button id="wishlist-btn" data-id="{{ $product->id }}" class="flex items-center gap-2 font-bold text-sm hover:opacity-75">
                       <!-- Line 150 -->
                        <i id="wishlist-icon" class="{{ Auth::user()->wishlist->contains('product_id', $product->id) ? 'fas text-red-500' : 'far text-gray-400' }} fa-heart transition-all"></i>

                        <!-- Line 151 -->
                        <span id="wishlist-text" class="text-gray-600">{{ Auth::user()->wishlist->contains('product_id', $product->id) ? 'Wishlisted' : 'Save' }}</span>
                    </button>

                    {{-- Set Alert Button --}}
                    <button id="alert-btn" data-id="{{ $product->id }}" class="flex items-center gap-2 font-bold text-sm text-emerald-600 hover:opacity-75">
                        <i class="fas fa-bell text-amber-500"></i>
                        <span id="alert-text">Set Alert</span>
                    </button>
                </div>
            @endauth

            <img src="{{ str_contains($product->image_url, 'http') ? $product->image_url : asset($product->image_url) }}" 
                 alt="{{ $product->name }}" 
                 class="w-48 h-48 object-contain bg-gray-50 rounded-2xl p-4">

            <div class="flex-1">
                <h1 class="text-3xl font-black text-gray-900">{{ $product->name }}</h1>
                
                {{-- Rating Form Section --}}
                <div class="mt-4">
                    @auth
                        <div class="mt-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                {{ ($userRating ?? 0) > 0 ? 'Your Rating' : 'Rate this product' }}
                            </h4>

                            <div class="flex items-center gap-3">
                                <form action="{{ route('product.rate', $product->id) }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer rating-star">
                                            <input type="radio" name="rating" value="{{ $i }}" class="hidden" onchange="this.form.submit()">
                                            <i class="fas fa-star text-xl {{ ($userRating ?? 0) >= $i ? 'text-amber-400' : 'text-gray-300 hover:text-amber-400' }}"></i>
                                        </label>
                                    @endfor
                                </form>

                                @if(($userRating ?? 0) > 0)
                                    <form action="{{ route('product.rate', $product->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="remove" value="1">
                                        <button type="submit" class="text-xs text-red-500 hover:underline font-bold">Clear</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endauth
                </div>

                @if(!empty($product->description))
                    <p class="text-gray-600 mt-4 text-sm leading-relaxed italic bg-gray-50 p-4 rounded-xl border border-gray-100">
                        {{ $product->description }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Notification Toast --}}
        <div id="toast" class="hidden fixed bottom-5 right-5 bg-gray-900 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 animate-bounce z-50">
            <i class="fas fa-bell text-amber-400"></i>
            <span id="toast-message"></span>
        </div>

        {{-- Vendor Grid --}}
        <h2 class="text-xl font-bold text-gray-900 mb-6">Available Vendors ({{ $product->vendors->count() }})</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($product->vendors as $vendor)
                @php
                    $latestHistory = $vendor->products->where('id', $product->id)->first()?->priceHistories->first();
                @endphp
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center hover:border-emerald-200 transition">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900">{{ $vendor->name }}</h3>
                            @if($vendor->verified_at)
                                <span class="text-emerald-500"><i class="fas fa-check-circle"></i></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i> {{ $vendor->location }}</p>
                        
                        @php
    $latestHistory = \App\Models\PriceHistory::where('product_id', $product->id)
                ->where('vendor_id', $vendor->id)
                ->orderBy('created_at', 'desc')
                ->skip(1)
                ->first();
@endphp

<div class="mt-3 flex items-baseline gap-2">
    @if($latestHistory && $latestHistory->price > $vendor->pivot->price)
        <span class="text-gray-400 line-through text-sm font-semibold">
            {{ number_format($latestHistory->price) }}
        </span>
    @endif
    <p class="text-lg font-black text-emerald-600">
        {{ number_format($vendor->pivot->price) }} <span class="text-sm font-normal text-gray-400">CFA</span>
    </p>
</div>
                        <p class="text-xs text-gray-500">
    Last updated: {{ $vendor->pivot->updated_at ? \Carbon\Carbon::parse($vendor->pivot->updated_at)->diffForHumans() : 'N/A' }}
</p>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <a href="tel:{{ $vendor->phone }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-emerald-600 hover:text-white transition text-center">
                            <i class="fas fa-phone-alt mr-1"></i> Call
                        </a>
                        <a href="https://wa.me/{{ $vendor->phone }}" target="_blank" class="bg-green-100 text-green-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-600 hover:text-white transition text-center">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="max-w-7xl mx-auto px-4 py-12">
            
            <!-- Section: More from this Shop -->
            @if(isset($vendorProducts) && $vendorProducts->isNotEmpty())
                <section class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 border-l-4 border-emerald-600 pl-4">
                        More from {{ $product->vendors->first()->name }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($vendorProducts as $otherProduct)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">
                                <div class="p-4">
                                    <img src="{{ $otherProduct->image_url ? asset($otherProduct->image_url) : 'https://via.placeholder.com/150' }}" 
                                         alt="{{ $otherProduct->name }}" 
                                         class="w-full h-32 object-contain mb-3">
                                    <h4 class="font-semibold text-gray-800 text-sm leading-tight">{{ $otherProduct->name }}</h4>
                                    <p class="text-emerald-600 font-bold text-sm mt-1">
                                        {{ number_format($otherProduct->vendors->first()->pivot->price ?? 0) }} FCFA
                                    </p>
                                    <a href="{{ route('product.show', $otherProduct->id) }}" 
                                       class="mt-2 block text-center text-xs bg-gray-900 hover:bg-emerald-700 text-white font-bold py-1.5 rounded-lg transition">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Section: Similar Products -->
            @if(isset($similarProducts) && $similarProducts->isNotEmpty())
                <section class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 border-l-4 border-amber-500 pl-4">
                        Similar Products
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($similarProducts as $similarProduct)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">
                                <div class="p-4">
                                    <img src="{{ $similarProduct->image_url ? asset($similarProduct->image_url) : 'https://via.placeholder.com/150' }}" 
                                         alt="{{ $similarProduct->name }}" 
                                         class="w-full h-32 object-contain mb-3">
                                    <h4 class="font-semibold text-gray-800 text-sm leading-tight">{{ $similarProduct->name }}</h4>
                                    <p class="text-emerald-600 font-bold text-sm mt-1">
                                        {{ number_format($similarProduct->vendors->first()->pivot->price ?? 0) }} FCFA
                                    </p>
                                    <a href="{{ route('product.show', $similarProduct->id) }}" 
                                       class="mt-2 block text-center text-xs bg-gray-900 hover:bg-emerald-700 text-white font-bold py-1.5 rounded-lg transition">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        @auth
        <script>
            const csrfToken = '{{ csrf_token() }}';
            const productId = '{{ $product->id }}';

            // Wishlist Toggle
            document.getElementById('wishlist-btn').addEventListener('click', function() {
                let icon = document.getElementById('wishlist-icon');
                let text = document.getElementById('wishlist-text');
                fetch(`/wishlist/toggle/${productId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
                }).then(() => {
                    icon.classList.toggle('fas'); icon.classList.toggle('far');
                    icon.classList.toggle('text-red-500'); icon.classList.toggle('text-gray-400');
                    text.innerText = icon.classList.contains('fas') ? 'Wishlisted' : 'Save';
                    showToast(icon.classList.contains('fas') ? 'Added to wishlist!' : 'Removed from wishlist.');
                });
            });

            // Price Alert Toggle
            document.getElementById('alert-btn').addEventListener('click', function() {
                fetch(`/alerts/toggle/${productId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
                }).then(() => {
                    showToast('Alert set! You will be emailed if the price drops.');
                });
            });

            function showToast(message) {
                let toast = document.getElementById('toast');
                document.getElementById('toast-message').innerText = message;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }
        </script>
        @endauth
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-white border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 text-lg">PriceCheckCameroon</h4>
                    <p class="text-sm text-gray-500">Compare prices from local stores and save money on your purchases.</p>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">Quick Links</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="{{ route('about') }}" class="hover:text-emerald-600 transition">About Us</a></li>
                        <!-- <li><a href="#" class="hover:text-emerald-600 transition">Contact</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Privacy Policy</a></li> -->
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">For Vendors</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="{{ route('vendor.register.form') }}" class="hover:text-emerald-600 transition">Register Store</a></li>
                        <li><a href="{{ route('vendor.login') }}" class="hover:text-emerald-600 transition">Vendor Login</a></li>
                        <!-- <li><a href="#" class="hover:text-emerald-600 transition">How It Works</a></li> -->
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">Support</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="{{ route('help') }}" class="hover:text-emerald-600 transition">Help Center</a></li>
                        <li> <a href="{{ route('faq') }}" class="hover:text-emerald-600 transition">FAQ</a></li>
                        <!-- <li><a href="#" class="hover:text-emerald-600 transition">Terms of Service</a></li> -->
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-6 pt-4 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} PriceCheckCameroon. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>