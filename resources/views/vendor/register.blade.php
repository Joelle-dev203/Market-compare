<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Business - PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .form-card { transition: all 0.3s ease; }
        .form-card:hover { box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1); }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { border-color: #059669; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ mobileMenu: false, categoryOpen: false, showPass: false, businessType: 'retail' }">

    <!-- ===== HEADER (STICKY) ===== -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('product.search') }}" class="text-2xl font-bold text-emerald-600 tracking-tight">
                PriceCheck<span class="text-amber-500">Cameroon</span>
            </a>
            
            <div class="hidden md:flex items-center space-x-6">
                @auth
                    <a href="{{ route('wishlist.index') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">
                        <i class="fas fa-heart mr-1"></i> My Wishlist
                    </a>
                @endauth

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

                @if(Auth::guard('vendor')->check())
                    <a href="{{ route('vendor.dashboard') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-800 transition">Dashboard</a>
                @else
                    <a href="{{ route('vendor.register.form') }}" class="flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                        <i class="fas fa-store mr-2"></i>Register Business
                    </a>
                @endif
                
                <a href="{{ route('about') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition">About</a>
            </div>

            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-bars text-gray-700 text-xl"></i>
            </button>
        </div>

        <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-t border-gray-100 py-4 px-4">
            <div class="flex flex-col space-y-3">
                <a href="{{ route('product.search') }}" class="text-gray-700 hover:text-emerald-600 font-medium">Home</a>
                <a href="{{ route('vendor.register.form') }}" class="text-emerald-600 font-medium">Register Business</a>
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
    <main class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-16">
        <div class="mb-4">
            <a href="{{ route('product.search') }}" class="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 transition">
                <span class="mr-1.5 text-base">←</span> Back to Search
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 form-card">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-store text-2xl text-emerald-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Register Business</h1>
                <p class="text-sm text-gray-500">Join our price engine to showcase your products or travel services.</p>
            </div>

           <!-- Success Alert Message -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-6 text-sm text-emerald-700 flex items-center">
                    <i class="fas fa-check-circle mr-2 text-emerald-600 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 mb-6 text-sm text-rose-600">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                <!-- Business Type Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Business Type</label>
                    <select name="type" x-model="businessType" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none text-sm input-focus">
                        <option value="retail">Retail Shop</option>
                        <option value="flight">Flight Agency</option>
                        <option value="bus">Bus Agency</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Business Name</label>
                    <input type="text" name="name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 input-focus text-sm" required>
                </div>


                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Logo / Profile Image Optional</label>
                    <input type="file" name="logo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                  
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 input-focus text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" name="phone_number" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 input-focus text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 input-focus text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 input-focus text-sm pr-10" required>
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 text-gray-400">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition mt-4 text-sm uppercase tracking-wide">
                    Register Business
                </button>

                <p class="text-center text-xs text-gray-500 mt-4">
                    Already registered? <a href="{{ route('vendor.login.form') }}" class="text-emerald-600 font-bold hover:underline">Log in here</a>
                </p>
            </form>
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