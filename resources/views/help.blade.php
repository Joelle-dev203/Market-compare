<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .help-card {
            transition: all 0.3s ease;
        }
        .help-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ mobileMenu: false }">

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
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Header Section (Without Search Bar) -->
        <div class="text-center mb-12">
            <div class="text-5xl mb-4">🛟</div>
            <h1 class="text-4xl font-extrabold text-emerald-800 mb-4">How can we help you today?</h1>
            <p class="text-gray-600 max-w-xl mx-auto">Browse our support topics and popular questions below to find what you're looking for.</p>
        </div>

        <!-- Quick Help Cards Grid -->
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            
            <!-- Card 1 -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 help-card text-center">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg mb-2">Buyer Support</h3>
                <p class="text-gray-500 text-sm mb-4">Learn how to search products, track price drops, and manage your wishlist.</p>
                <a href="{{ route('faq') }}" class="text-emerald-600 font-semibold text-sm hover:underline">View buyer FAQs &rarr;</a>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 help-card text-center">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg mb-2">Vendor Support</h3>
                <p class="text-gray-500 text-sm mb-4">Get guidance on managing listings, updating inventory prices, and dashboard access.</p>
                <a href="{{ route('faq') }}" class="text-amber-600 font-semibold text-sm hover:underline">View vendor guides &rarr;</a>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 help-card text-center">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg mb-2">Account & Security</h3>
                <p class="text-gray-500 text-sm mb-4">Troubleshoot login issues, password resets, and session redirects.</p>
                <a href="{{ route('login') }}" class="text-blue-600 font-semibold text-sm hover:underline">Manage account &rarr;</a>
            </div>

        </div>

        <!-- Popular Articles / Common Issues Section -->
        <div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-gray-100 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-fire text-amber-500 mr-3"></i> Popular Help Topics
            </h2>

            <div class="divide-y divide-gray-100 space-y-4">
                
                <div class="pt-4 flex items-start justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800 text-base mb-1">Do I need an account to compare prices?</h4>
                        <p class="text-gray-500 text-sm">Learn how you can search and view prices freely without logging in, and when an account is required.</p>
                    </div>
                    <a href="{{ route('faq') }}" class="text-emerald-600 text-sm font-semibold hover:underline shrink-0 ml-4">Read article</a>
                </div>

                <div class="pt-4 flex items-start justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800 text-base mb-1">How do price-drop email alerts work?</h4>
                        <p class="text-gray-500 text-sm">Learn how adding items to your wishlist automatically triggers notification alerts when prices decrease.</p>
                    </div>
                    <a href="{{ route('faq') }}" class="text-emerald-600 text-sm font-semibold hover:underline shrink-0 ml-4">Read article</a>
                </div>

                <div class="pt-4 flex items-start justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800 text-base mb-1">How do I update or delete my vendor price listings?</h4>
                        <p class="text-gray-500 text-sm">Step-by-step instructions for managing your active shop products and keeping pricing data accurate.</p>
                    </div>
                    <a href="{{ route('faq') }}" class="text-emerald-600 text-sm font-semibold hover:underline shrink-0 ml-4">Read article</a>
                </div>

            </div>
        </div>

        <!-- Still need help? Contact CTA -->
        <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-3xl p-8 md:p-12 text-center text-white shadow-lg">
            <h2 class="text-3xl font-extrabold mb-3">Still have questions?</h2>
            <p class="text-emerald-100 mb-6 max-w-lg mx-auto">If you couldn't find the answer you were looking for, our support team is ready to help you out.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('faq') }}" class="bg-white text-emerald-800 px-6 py-3 rounded-xl font-bold hover:bg-emerald-50 transition shadow-sm">
                    Browse Full FAQ
                </a>
                <a href="#" class="bg-emerald-600 border border-emerald-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-500 transition shadow-sm">
                    Contact Support
                </a>
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
                        <!-- <li><a href="{{ route('faq') }}" class="hover:text-emerald-600 transition">How It Works</a></li> -->
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
                &copy; {{ date('Y') }} PriceCheckCameroon. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>