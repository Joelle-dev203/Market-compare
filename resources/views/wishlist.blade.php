<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | PriceCheck Cameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .wishlist-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .wishlist-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }
        .remove-btn {
            transition: all 0.3s ease;
        }
        .remove-btn:hover {
            transform: scale(1.1);
            color: #dc2626 !important;
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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">My Wishlist</h1>
                <p class="text-gray-500 text-sm mt-1">Products and trips you've saved for later</p>
            </div>
            <span class="bg-emerald-100 text-emerald-800 font-bold px-4 py-1.5 rounded-full text-sm">
                <i class="fas fa-heart mr-1"></i> {{ $products->count() + ($trips ?? collect())->count() }} Items
            </span>
        </div>

        @if($products->isEmpty() && ($trips ?? collect())->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-heart text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Your wishlist is empty</h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">Save items or trips you like while searching and they will appear here.</p>
                <a href="{{ route('product.search') }}" class="inline-block bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-search mr-2"></i> Start Browsing
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- LOOP THROUGH PRODUCTS -->
                @foreach($products as $product)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 wishlist-card overflow-hidden">
                        <div class="relative">
                            <img src="{{ $product->image_url ? asset($product->image_url) : 'https://via.placeholder.com/300x200/10b981/ffffff?text=PrixCameroon' }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-48 object-cover">
                        </div>
                        <div class="p-5">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                                {{ $product->category ?? 'General' }}
                            </span>
                            <h3 class="font-bold text-gray-900 text-lg mt-2 leading-tight">{{ $product->name }}</h3>
                            
                            <div class="mt-3 flex items-center justify-between">
                                <p class="text-sm font-black text-emerald-600">
                                    @if($product->vendors->isNotEmpty())
                                        {{ number_format($product->vendors->first()->pivot->price) }} <span class="text-xs font-normal text-gray-400">FCFA</span>
                                    @else
                                        <span class="text-gray-400">Price N/A</span>
                                    @endif
                                </p>
                                <a href="{{ route('product.show', $product->id) }}" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm transition">
                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            

                            @if($product->vendors->isNotEmpty())
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                                    <i class="fas fa-store"></i>
                                    <span>{{ $product->vendors->first()->name }}</span>
                                </div>
                            @endif

                            <!-- DELETE MODAL TRIGGER BUTTON -->
                            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                <button type="button" 
                                        onclick="openDeleteModal('{{ route('wishlist.destroy', $product->id) }}')" 
                                        class="text-red-500 hover:text-red-700 text-sm transition p-2 flex items-center gap-1 font-medium">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- LOOP THROUGH TRIPS -->
                @if(isset($trips))
                    @foreach($trips as $item)
                        @if($item->trip)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 wishlist-card overflow-hidden">
                                <!-- Agency Logo / Fallback Header -->
                                <div class="relative bg-gray-50 h-48 flex items-center justify-center overflow-hidden border-b border-gray-100">
                                    @if($item->trip->agency && $item->trip->agency->logo_path)
                                        <img src="{{ asset('storage/' . $item->trip->agency->logo_path) }}" 
                                             alt="{{ $item->trip->agency->name ?? 'Agency' }}" 
                                             class="h-full w-full object-contain p-4">
                                    @else
                                        <div class="text-center p-4">
                                            <i class="fas fa-bus text-4xl mb-2 text-emerald-600"></i>
                                            <p class="font-bold uppercase tracking-wider text-xs text-gray-600">{{ $item->trip->agency->name ?? 'Travel Agency' }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-5">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full">
                                        {{ $item->trip->type ?? 'Trip' }}
                                    </span>
                                    <h3 class="font-bold text-gray-900 text-lg mt-2 leading-tight">
                                        {{ $item->trip->route->departure_city ?? 'Origin' }} 
                                        <i class="fas fa-arrow-right text-xs mx-1 text-gray-400"></i> 
                                        {{ $item->trip->route->arrival_city ?? 'Destination' }}
                                    </h3>
                                    
                                    <div class="mt-4 flex items-center justify-between pt-3 border-t border-gray-100">
                                        <div class="flex items-center gap-2 text-xs text-gray-600 font-medium">
                                            <i class="fas fa-building text-emerald-600"></i>
                                            <span>{{ $item->trip->agency->name ?? 'Agency' }}</span>
                                        </div>
                                        <a href="{{ route('trips.show', $item->trip->id) }}" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm transition">
                                            View Trip <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>

                                    <!-- DELETE MODAL TRIGGER BUTTON -->
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                        <button type="button" 
                                                onclick="openDeleteModal('{{ route('wishlist.destroy', $item->id) }}')" 
                                                class="text-red-500 hover:text-red-700 text-sm transition p-2 flex items-center gap-1 font-medium">
                                            <i class="fas fa-trash-alt"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

            </div>
        @endif
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-white border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 text-lg">PrixCameroon</h4>
                    <p class="text-sm text-gray-500">Compare prices from local stores and save money on your purchases.</p>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">Quick Links</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="{{ route('about') }}" class="hover:text-emerald-600 transition">About Us</a></li>
                       
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">For Vendors</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="{{ route('vendor.register.form') }}" class="hover:text-emerald-600 transition">Register Store</a></li>
                        <li><a href="{{ route('vendor.login') }}" class="hover:text-emerald-600 transition">Vendor Login</a></li>
                        
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">Support</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="{{ route('help') }}" class="hover:text-emerald-600 transition">Help Center</a></li>
                       <li> <a href="{{ route('faq') }}" class="hover:text-emerald-600 transition">FAQ</a></li>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-6 pt-4 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} PrixCameroon. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- ===== CUSTOM DELETE CONFIRMATION MODAL ===== -->
    <div id="deleteConfirmationModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-xl border border-gray-100 transform transition-all scale-95 opacity-0 duration-200" id="modalContainer">
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-900 text-center mb-2">Remove Item?</h3>
            <p class="text-gray-500 text-sm text-center mb-6">Are you sure you want to remove this item from your wishlist and alerts? This action cannot be undone.</p>
            
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-bold hover:bg-gray-200 transition text-sm">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-md hover:shadow-lg text-sm">
                        Yes, Remove
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Script Controls -->
    <script>
        function openDeleteModal(deleteUrl) {
            const modal = document.getElementById('deleteConfirmationModal');
            const container = document.getElementById('modalContainer');
            const form = document.getElementById('deleteForm');

            form.action = deleteUrl;
            modal.classList.remove('hidden');
            
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmationModal');
            const container = document.getElementById('modalContainer');

            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        window.onclick = function(event) {
            const modal = document.getElementById('deleteConfirmationModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>