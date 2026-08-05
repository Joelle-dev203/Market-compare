<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequently Asked Questions - PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .faq-card {
            transition: all 0.3s ease;
        }
        .faq-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ mobileMenu: false, activeTab: 'buyers' }">

    <!-- ===== HEADER (STICKY) ===== -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('product.search') }}" class="text-2xl font-bold text-emerald-600 tracking-tight">
                PriceCheck<span class="text-amber-500">Cameroon</span>
            </a>
            
            <div class="hidden md:flex items-center space-x-6">
                <!-- Wishlist -->
                @auth
                    <a href="{{ route('wishlist.index') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">
                        <i class="fas fa-heart mr-1"></i> My Wishlist
                    </a>
                @endauth

                <!-- User Actions -->
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

                <!-- Vendor -->
                @if(Auth::guard('vendor')->check())
                    <a href="{{ route('vendor.dashboard') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-800 transition">Dashboard</a>
                @else
                    <a href="{{ route('vendor.register.form') }}" class="flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                        <i class="fas fa-store mr-2"></i>Register Shop
                    </a>
                @endif
                
                <a href="{{ route('about') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">About</a>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-bars text-gray-700 text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-t border-gray-100 py-4 px-4">
            <div class="flex flex-col space-y-3">
                <a href="{{ route('product.search') }}" class="text-gray-700 hover:text-emerald-600 font-medium">Home</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-emerald-600 font-medium">About</a>
                <a href="{{ route('vendor.register.form') }}" class="text-emerald-600 font-medium">Sell</a>
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-red-500 font-medium text-left w-full">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 font-medium">Login</a>
                @endauth
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
        <div class="text-center mb-10">
            <div class="text-5xl mb-4">❓</div>
            <h1 class="text-4xl font-extrabold text-emerald-800 mb-3">Frequently Asked Questions</h1>
            <p class="text-lg text-gray-600">Find quick answers about searching, comparing prices, and managing your listings.</p>
        </div>

        <!-- Toggle Switch / Tabs -->
        <div class="flex justify-center mb-10">
            <div class="bg-gray-200 p-1.5 rounded-2xl flex space-x-2 shadow-inner">
                <button @click="activeTab = 'buyers'" 
                    :class="activeTab === 'buyers' ? 'bg-white text-emerald-800 shadow-md font-bold' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-6 py-2.5 rounded-xl transition text-sm">
                    <i class="fas fa-shopping-bag mr-2"></i> For Buyers
                </button>
                <button @click="activeTab = 'vendors'" 
                    :class="activeTab === 'vendors' ? 'bg-white text-amber-800 shadow-md font-bold' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-6 py-2.5 rounded-xl transition text-sm">
                    <i class="fas fa-store mr-2"></i> For Vendors, Airlines & Agencies
                </button>
            </div>
        </div>

        <!-- FAQ Content Area -->
        <div class="space-y-6">

            <!-- BUYERS SECTION -->
            <div x-show="activeTab === 'buyers'" x-cloak class="space-y-4">
                
                <!-- Q1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>What is this platform?</span>
                        <i :class="open ? 'rotate-180 text-emerald-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        It's a website where you can search for a product, a flight, or a bus ticket and see prices from different shops, airlines, or bus agencies side by side, so you can pick the cheapest option.
                    </p>
                </div>

                <!-- Q2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>Do I need an account to search and compare prices?</span>
                        <i :class="open ? 'rotate-180 text-emerald-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        No. Anyone can search and view prices without logging in. You only need an account to save items to your wishlist or get price-drop email alerts.
                    </p>
                </div>

                <!-- Q3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>How current are the prices shown?</span>
                        <i :class="open ? 'rotate-180 text-emerald-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        Every price has a "last updated" timestamp. If a price hasn't been refreshed in a while, it's marked as possibly outdated so you know to double-check before relying on it.
                    </p>
                </div>

                <!-- Q4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>What if the price I see doesn't match what the shop/company actually charges?</span>
                        <i :class="open ? 'rotate-180 text-emerald-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        Prices are submitted by vendors, airlines, and bus agencies themselves, so occasional mismatches can happen. You can report a price that turns out to be wrong, and it will be reviewed.
                    </p>
                </div>

                <!-- Q5 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>How does the wishlist and price alerts work?</span>
                        <i :class="open ? 'rotate-180 text-emerald-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        Save any product, flight, or bus route you're interested in. The platform remembers the price at that moment, checks regularly for changes, and automatically emails you if its price drops below what it was when you saved it. Your email is used strictly for these alerts.
                    </p>
                </div>

                <!-- Q6 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>Is the platform free to use for buyers?</span>
                        <i :class="open ? 'rotate-180 text-emerald-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        Yes, searching, comparing, and using the wishlist are all completely free. The platform is also built to be lightweight and mobile-first.
                    </p>
                </div>

            </div>

            <!-- VENDORS SECTION -->
            <div x-show="activeTab === 'vendors'" x-cloak class="space-y-4">
                
                <!-- V1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>How do I list my prices on the platform?</span>
                        <i :class="open ? 'rotate-180 text-amber-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        Register for a vendor account, then submit or update your prices through a simple form directly from your dashboard. Make sure you are logged in using your vendor credentials.
                    </p>
                </div>

                <!-- V2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>How often should I update my prices?</span>
                        <i :class="open ? 'rotate-180 text-amber-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        As often as they change. Prices not refreshed within a set time are automatically flagged as outdated to buyers, which can affect whether they trust or choose your listing.
                    </p>
                </div>

                <!-- V3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 faq-card" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-bold text-gray-800 text-lg">
                        <span>Can I remove or edit a price I already submitted?</span>
                        <i :class="open ? 'rotate-180 text-amber-600' : 'text-gray-400'" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <p x-show="open" x-cloak class="mt-3 text-gray-600 text-sm leading-relaxed border-t pt-3">
                        Yes, you have full control to edit or delete prices through your vendor dashboard at any time.
                    </p>
                </div>

            </div>

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