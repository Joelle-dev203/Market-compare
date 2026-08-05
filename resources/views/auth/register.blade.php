<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Login / Register | PriceCheck Cameroon</title>
    <style>
        [x-cloak] { display: none !important; }
        .form-card {
            transition: all 0.3s ease;
        }
        .form-card:hover {
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
        }
        .benefit-card {
            transition: all 0.3s ease;
        }
        .benefit-card:hover {
            transform: translateX(4px);
        }
        .input-focus {
            transition: all 0.3s ease;
        }
        .input-focus:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
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

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-t border-gray-100 py-4 px-4">
            <div class="flex flex-col space-y-3">
                <a href="{{ route('product.search') }}" class="text-gray-700 hover:text-emerald-600 font-medium">Home</a>
                <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium">Products</a>
                <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium">Stores</a>
                <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium">Deals</a>
                <a href="{{ route('vendor.register.form') }}" class="text-emerald-600 font-medium">Sell</a>
                @auth
                    <a href="#" class="text-red-500 font-medium">Logout</a>
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
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
            
            <!-- Left Column: Forms -->
            <div x-data="{ tab: 'login', showPass: false, showConfirm: false }" class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 form-card">
                
                <!-- Tab Buttons -->
                <div class="flex mb-8 border-b">
                    <button @click="tab = 'login'" :class="tab === 'login' ? 'border-b-2 border-emerald-600 font-bold text-emerald-600' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-3 text-sm transition">Login</button>
                    <button @click="tab = 'register'" :class="tab === 'register' ? 'border-b-2 border-emerald-600 font-bold text-emerald-600' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-3 text-sm transition">Register</button>
                </div>

                <!-- Login Form -->
                <form x-show="tab === 'login'" action="{{ route('login') }}" method="POST">
                    @csrf
                    <h2 class="text-2xl font-black text-gray-900 mb-6">Welcome back!</h2>
                    
                    <input type="email" name="email" placeholder="Email Address" required class="w-full p-4 border rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none input-focus">
                    @error('email') <p class="text-red-500 text-xs mt-1 mb-3">{{ $message }}</p> @enderror
                    
                    <div class="relative mt-4 mb-6">
                        <input :type="showPass ? 'text' : 'password'" name="password" placeholder="Password" required class="w-full p-4 border rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none input-focus">
                        <button type="button" @click="showPass = !showPass" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs -mt-5 mb-4">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full bg-emerald-600 text-white p-4 rounded-2xl font-bold hover:bg-emerald-700 transition shadow-md hover:shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                    
                    <p class="text-center text-sm text-gray-500 mt-4">
                        Don't have an account? 
                        <button type="button" @click="tab = 'register'" class="text-emerald-600 font-bold hover:underline">Register</button>
                    </p>
                </form>

                <!-- Register Form -->
                <form x-show="tab === 'register'" x-cloak action="{{ route('register') }}" method="POST">
                    @csrf
                    <h2 class="text-2xl font-black text-gray-900 mb-6">Create account</h2>
                    
                    <input type="text" name="name" placeholder="Full Name" required class="w-full p-4 mb-1 border rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none input-focus">
                    @error('name') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

                    <input type="email" name="email" placeholder="Email Address" required class="w-full p-4 mb-1 border rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none input-focus">
                    @error('email') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror
                    
                    <div class="relative mb-1">
                        <input :type="showPass ? 'text' : 'password'" name="password" placeholder="Password" required class="w-full p-4 border rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none input-focus">
                        <button type="button" @click="showPass = !showPass" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

                    <div class="relative mb-6">
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" placeholder="Confirm Password" required class="w-full p-4 border rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none input-focus">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition">
                            <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 text-white p-4 rounded-2xl font-bold hover:bg-emerald-700 transition shadow-md hover:shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i> Complete Registration
                    </button>
                    
                    <p class="text-center text-sm text-gray-500 mt-4">
                        Already have an account? 
                        <button type="button" @click="tab = 'login'" class="text-emerald-600 font-bold hover:underline">Login</button>
                    </p>
                </form>
            </div>

            <!-- Right Column: Benefits -->
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm self-start form-card">
                <h3 class="text-2xl font-extrabold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-gem text-emerald-500 mr-3"></i>
                    Why Sign into PriceCheck Cameroon?
                </h3>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start benefit-card">
                        <i class="fas fa-check-circle text-emerald-600 mt-1 mr-3 text-lg"></i>
                        <span>Add products you will love to buy on your wishlist</span>
                    </li>
                    <li class="flex items-start benefit-card">
                        <i class="fas fa-check-circle text-emerald-600 mt-1 mr-3 text-lg"></i>
                        <span>Access your wishlist anytime, anywhere</span>
                    </li>
                    <li class="flex items-start benefit-card">
                        <i class="fas fa-check-circle text-emerald-600 mt-1 mr-3 text-lg"></i>
                        <span>Set an alert to receive an email when the price drops</span>
                    </li>
                    <li class="flex items-start benefit-card">
                        <i class="fas fa-check-circle text-emerald-600 mt-1 mr-3 text-lg"></i>
                        <span>Rate products you have used and help others</span>
                    </li>
                </ul>
                
                <div class="mt-8 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <p class="text-sm text-emerald-800 font-medium">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Your data is safe and secure with us.
                    </p>
                </div>
            </div>
        </div>
    </div>

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
                        <li><a href="#" class="hover:text-emerald-600 transition">Contact</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">For Vendors</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="{{ route('vendor.register.form') }}" class="hover:text-emerald-600 transition">Register Store</a></li>
                        <li><a href="{{ route('vendor.login') }}" class="hover:text-emerald-600 transition">Vendor Login</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">How It Works</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">Support</h5>
                    <ul class="space-y-1 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600 transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Terms of Service</a></li>
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