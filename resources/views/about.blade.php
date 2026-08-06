<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .about-card {
            transition: all 0.3s ease;
        }
        .about-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }
        .mission-icon {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
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
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <div class="mission-icon text-6xl mb-4">🇨🇲</div>
            <h1 class="text-4xl font-extrabold text-emerald-800 mb-4">About PrixCameroon</h1>
            <p class="text-xl text-gray-600">Bringing transparency to the local market, one price at a time.</p>
        </div>

        <div class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100 space-y-8 about-card">
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bullseye text-emerald-600 mr-3"></i> Our Mission
                </h2>
                <p class="leading-relaxed text-gray-600">
                    PrixCameroon was created to empower Cameroonian consumers. In a market where prices for the same 
                    everyday goods can fluctuate wildly between neighborhoods and vendors, we believe that 
                    <strong class="text-emerald-700">knowledge is power</strong>. Our platform aggregates price data to help you save 
                    money and find the best deals in your area.
                </p>
            </section>

            <section class="grid md:grid-cols-2 gap-6">
                <div class="p-6 bg-emerald-50 rounded-2xl hover:shadow-md transition">
                    <div class="text-3xl mb-3">🛒</div>
                    <h3 class="font-bold text-emerald-800 mb-2">For Consumers</h3>
                    <p class="text-sm text-emerald-700 leading-relaxed">
                        Easily search for products, compare prices across multiple local shops, and find the most affordable options near you.
                    </p>
                </div>
                <div class="p-6 bg-amber-50 rounded-2xl hover:shadow-md transition">
                    <div class="text-3xl mb-3">🏪</div>
                    <h3 class="font-bold text-amber-800 mb-2">For Vendors</h3>
                    <p class="text-sm text-amber-700 leading-relaxed">
                        List your shop's inventory, update your prices in real-time, and get discovered by local customers searching for your products.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-eye text-emerald-600 mr-3"></i> Our Vision
                </h2>
                <p class="leading-relaxed text-gray-600">
                    We envision a more connected digital economy in Cameroon, where small businesses and 
                    everyday shoppers can bridge the gap through accurate, real-time market data. We are 
                    committed to growing our database to cover every region, ensuring fair pricing for everyone.
                </p>
            </section>

            <!-- Core Values -->
            <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                <div class="text-center p-4">
                    <i class="fas fa-shield-alt text-emerald-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-800 text-sm">Trust & Transparency</p>
                </div>
                <div class="text-center p-4">
                    <i class="fas fa-hand-holding-heart text-emerald-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-800 text-sm">Community First</p>
                </div>
                <div class="text-center p-4">
                    <i class="fas fa-rocket text-emerald-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-800 text-sm">Innovation</p>
                </div>
            </section>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('product.search') }}" class="inline-flex items-center text-emerald-600 font-semibold hover:text-emerald-800 transition group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition"></i> Back to Search
            </a>
        </div>
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
                 <li><a href="{{ route('faq') }}" class="hover:text-emerald-600 transition">FAQ</a></li>
                        <!-- <li><a href="#" class="hover:text-emerald-600 transition">Terms of Service</a></li> -->
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-6 pt-4 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} PrixCameroon. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>