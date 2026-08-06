<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Compare Prices Cameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .category-chip {
            transition: all 0.3s ease;
        }
        .category-chip:hover {
            background: #059669 !important;
            color: white !important;
            transform: scale(1.05);
        }
        .search-shadow {
            box-shadow: 0 10px 40px rgba(5, 150, 105, 0.15);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 30%, #047857 60%, #059669 100%);
        }
        .floating-shapes {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            pointer-events: none;
        }
        .price-tag {
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ mobileMenu: false, categoryOpen: false, searchTerm: '{{ $searchTerm ?? '' }}' }">

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

    <!-- ===== CATEGORY NAV (STICKY) ===== --><nav class="bg-gray-900 text-white py-3 shadow-md sticky top-16 z-40" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center space-x-8">
        <button @click="open = !open" class="flex items-center text-sm font-bold uppercase tracking-wider hover:text-amber-400 transition">
            <i class="fas fa-bars mr-2"></i> All Categories <i class="fas fa-chevron-down ml-2 text-[10px]"></i>
        </button>

        <a href="{{ route('product.search') }}" class="text-sm font-bold hover:text-amber-400 transition">All Items</a>
        <!-- <a href="#" class="text-sm font-bold hover:text-amber-400 transition">Flights</a>
        <a href="#" class="text-sm font-bold hover:text-amber-400 transition">Buses</a> -->
        <a href="#" class="text-sm font-bold hover:text-amber-400 transition">Top Deals</a>
    </div>

    <!-- Mega Menu Overlay (Wide Layout) -->
    <div x-show="open" @click.away="open = false" class="absolute left-0 w-full bg-white text-gray-800 shadow-2xl py-8 z-50 border-t border-gray-100" x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-3 gap-8">
            @php
                $menu = [
                    'Electronics' => ['Computers', 'Phones', 'Cameras'],
                    'Grocery' => ['Fruits', 'Vegetables', 'Beverages'],
                    'Jewelries' => ['Necklaces', 'Rings', 'Watches'],
                    'Clothing' => ['Men', 'Women', 'Shoes','Handbags'],
                    'Travel' => ['Flights', 'Buses', 'Car Rental'],
                    'Home' => ['Furniture', 'Kitchen', 'Decor'],
                    'Health & Beauty' => ['Skincare', 'Makeup', 'Fragrances']
                ];
            @endphp

            @foreach($menu as $category => $items)
                <div class="mb-4">
                    <p class="text-sm font-black text-emerald-600 uppercase tracking-widest mb-3 border-b pb-2">{{ $category }}</p>
                    <div class="space-y-1">
                        @foreach($items as $item)
                            <a href="{{ route('product.search', array_merge(request()->query(), ['category' => $item])) }}" 
                               class="block text-sm {{ request('category') == $item ? 'text-emerald-700 font-bold underline' : 'text-gray-600' }} hover:text-emerald-600 hover:underline transition">
                               {{ $item }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-gradient py-16 px-4 relative overflow-hidden shadow-inner">
        <div class="floating-shapes w-96 h-96 bg-white -top-20 -right-20"></div>
        <div class="floating-shapes w-64 h-64 bg-white bottom-20 -left-20"></div>
        
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <div class="inline-block bg-white/20 backdrop-blur-sm text-white text-sm font-bold px-4 py-1.5 rounded-full mb-6 border border-white/30">
                Find the Cheapest Prices in Cameroon
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 tracking-tight leading-tight">
                Find the Cheapest Prices in Cameroon
            </h1>
            <p class="text-emerald-100 mb-8 text-sm sm:text-base max-w-2xl mx-auto">
                Search everyday products, travel trips, and local deals across online markets and local stores.
            </p>
            
            <form action="{{ route('product.search') }}" method="GET" class="bg-white p-2 rounded-2xl search-shadow flex flex-col md:flex-row gap-2 max-w-3xl mx-auto">
                <div class="flex-1 flex items-center px-3">
                    <i class="fas fa-search text-gray-400 mr-2"></i>
                    <input type="text" name="query" value="{{ $searchTerm ?? '' }}" placeholder="Search for items, buses, flights..." class="w-full py-3 text-gray-900 font-medium outline-none text-base">
                </div>
                <div class="border-t md:border-t-0 md:border-l border-gray-200 flex items-center px-3">
                    <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                    <select name="location" class="w-full md:w-48 py-3 bg-transparent text-gray-600 outline-none text-sm font-medium">
                        <option value="">All Locations</option>
                        @foreach($availableLocations ?? [] as $loc)
                            <option value="{{ $loc }}" {{ isset($locationFilter) && $locationFilter == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-8 py-3 rounded-xl transition shadow-md text-sm uppercase tracking-wider whitespace-nowrap">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </form>

            <!-- Trust Badges -->
            <div class="flex flex-wrap justify-center gap-6 mt-8 text-white/80 text-sm">
    <span class="flex items-center gap-2"><i class="fas fa-check-circle text-amber-400"></i> {{ \App\Models\Product::count() }}+ Products</span>
<span class="flex items-center gap-2"><i class="fas fa-check-circle text-amber-400"></i> {{ \App\Models\Vendor::count() }}+ vendor</span>
    <span class="flex items-center gap-2"><i class="fas fa-check-circle text-amber-400"></i> Best Price Guarantee</span>
</div>
        </div>
    </section>

    <!-- ===== FEATURED PRODUCTS & TRIPS ===== -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">
        {{ $searchTerm || $locationFilter ? 'Showing results for: ' . ($searchTerm ?: 'All Items') : 'All Market Items' }}
    </h2>


<!-- TRIPS SECTION -->
{{-- Only show if NO product category is selected AND we are not searching for a specific product --}}
@php
    $search = strtolower(request('query', ''));
    $category = request('category');
    $isTransportSearch = empty($search) || str_contains($search, 'flight') || str_contains($search, 'bus') || str_contains($search, 'trip') || str_contains($search, 'travel') || in_array($category, ['Flights', 'Buses', 'Car Rental']);
@endphp

@if($isTransportSearch && isset($trips) && $trips->isNotEmpty())
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Available Agencies & Trips</h2>
    
    @php
        // Group the trips by agency ID so each card represents a unique agency
        $groupedTrips = $trips->groupBy(fn($trip) => $trip->agency_id ?? 'unknown');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12" x-data="{ activeAgencyModal: null }">
        @foreach($groupedTrips as $agencyId => $agencyTripsCollection)
            @php
                $firstTrip = $agencyTripsCollection->first();
                $agency = $firstTrip->agency;
                // Gather unique trip types for this agency to display on the card
                $tripTypes = $agencyTripsCollection->pluck('type')->unique()->filter()->implode(', ');
                if(empty($tripTypes)) {
                    $tripTypes = 'Trip / Transport';
                }

                // Calculate ratings safely based on the agency model
                $avgRating = ($agency && method_exists($agency, 'averageRating')) ? $agency->averageRating() : 0;
                $ratingsCount = ($agency && method_exists($agency, 'ratings')) ? $agency->ratings()->count() : 0;
                $avg = round($avgRating * 2) / 2;
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 card-hover overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="relative bg-gray-50 p-4">
                        <div class="h-44 bg-gray-50 flex items-center justify-center relative">
                            @if($agency && $agency->logo_path)
                                <img src="{{ asset('storage/' . $agency->logo_path) }}" 
                                     alt="{{ $agency->name ?? 'Agency' }}" 
                                     class="h-full w-full object-contain p-2">
                            @else
                                <i class="fas fa-building text-4xl text-emerald-600"></i>
                            @endif
                            
                            <!-- Verified Checkmark Badge -->
                            @if($agency && $agency->verified_at)
                                <span class="absolute top-2 right-12 z-10 bg-white rounded-full p-1 shadow-md flex items-center justify-center">
                                    <i class="fas fa-check-circle text-emerald-600 text-lg" title="Verified Agency"></i>
                                </span>
                            @endif

                            <!-- Favorite / Heart Button with Auth Check -->
                            @auth
                                <button type="button" 
                                        @click.stop="activeAgencyModal = 'agency-{{ $agencyId }}'" 
                                        class="absolute top-2 right-2 bg-white/90 hover:bg-white p-2 rounded-full shadow-md transition z-20 cursor-pointer"
                                        title="View Trips">
                                    <i class="far fa-heart text-gray-600 hover:text-red-500"></i>
                                </button>
                            @else
                                <button type="button" 
                                        onclick="openAuthModal()" 
                                        class="absolute top-2 right-2 bg-white/90 hover:bg-white p-2 rounded-full shadow-md transition z-20 cursor-pointer"
                                        title="Sign in to save">
                                    <i class="far fa-heart text-gray-600 hover:text-red-500"></i>
                                </button>
                            @endauth
                        </div>
                        
                        <div class="absolute top-2 left-2">
                            <span class="inline-flex items-center bg-emerald-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-lg">
                                <i class="fas fa-bus mr-1"></i> {{ $agencyTripsCollection->count() }} {{ $agencyTripsCollection->count() == 1 ? 'Trip' : 'Trips' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                                {{ $tripTypes }}
                            </span>

                            <!-- Star Ratings Section -->
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $avg) 
                                        <i class="fas fa-star text-amber-400 text-xs"></i>
                                    @elseif($i - 0.5 == $avg) 
                                        <i class="fas fa-star-half-alt text-amber-400 text-xs"></i>
                                    @else 
                                        <i class="far fa-star text-gray-300 text-xs"></i> 
                                    @endif
                                @endfor
                                <span class="text-[10px] text-gray-400 ml-1">({{ $ratingsCount }})</span>
                            </div>
                        </div>

                        <h3 class="font-bold text-gray-900 text-lg leading-tight mb-2">
                            {{ $agency->name ?? 'Transport Agency' }}
                        </h3>
                        <p class="text-xs text-gray-500 mb-4">
                            Explore available routes and schedules offered by this agency.
                        </p>
                    </div>
                </div>

                <div class="p-5 pt-0">
                    <button type="button" 
                            @click="activeAgencyModal = 'agency-{{ $agencyId }}'" 
                            class="w-full bg-gray-900 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl transition text-sm text-center uppercase tracking-wider">
                        View Details
                    </button>
                </div>
            </div>

            <!-- Modal / Drawer for Agency Trips Selection -->
            <div x-show="activeAgencyModal === 'agency-{{ $agencyId }}'" 
                 class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 style="display: none;"
                 x-transition.opacity>
                
                <div @click.away="activeAgencyModal = null" 
                     class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden border border-gray-100 max-h-[90vh] flex flex-col">
                    
                    <!-- Modal Header -->
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="flex items-center gap-3">
                            @if($agency && $agency->logo_path)
                                <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="" class="w-12 h-12 object-contain rounded-xl border bg-white p-1">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                    <i class="fas fa-building"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">{{ $agency->name ?? 'Agency' }}</h3>
                                <p class="text-xs text-gray-500 font-medium">Select a specific trip below to view full details</p>
                            </div>
                        </div>
                        <button @click="activeAgencyModal = null" class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300 transition font-bold">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Modal Body (List of Trips) -->
                    <div class="p-6 overflow-y-auto space-y-4 flex-1">
                        @foreach($agencyTripsCollection as $modalTrip)
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 hover:border-emerald-500 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                                            {{ $modalTrip->type ?? 'Trip' }}
                                        </span>
                                    </div>
                                    <h4 class="font-extrabold text-gray-900 text-base">
                                        {{ $modalTrip->route->departure_city ?? 'Origin' }} 
                                        <i class="fas fa-arrow-right text-emerald-500 mx-1 text-xs"></i> 
                                        {{ $modalTrip->route->arrival_city ?? 'Destination' }}
                                    </h4>
                                    <p class="text-xs text-gray-500 font-medium">
                                        {{ $modalTrip->name }}
                                    </p>
                                </div>
                                
                                <div class="w-full sm:w-auto">
                                    <div class="text-xs font-bold text-gray-700 text-center mb-1.5">{{ $agency->name ?? 'Agency' }}</div>
                                    <a href="{{ route('trips.show', $modalTrip->id) }}" 
                                       class="block w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-center transition shadow-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 bg-gray-50 border-t border-gray-100 text-right">
                        <button @click="activeAgencyModal = null" class="px-5 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-200 transition">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- PRODUCTS SECTION -->
{{-- Only show if we are not specifically searching for a trip/flight --}}
@if(isset($products) && $products->isNotEmpty())
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Market Items</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $product)
            @foreach($product->vendors as $vendor)
                @php
                    $latestHistory = $product->priceHistories()->where('vendor_id', $vendor->id)->orderBy('created_at', 'desc')->first();
                @endphp
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 card-hover overflow-hidden flex flex-col justify-between">
                    <div class="relative bg-gray-50 p-4">
                        <img src="{{ $product->image_url ?? 'https://via.placeholder.com/300x200/10b981/ffffff?text=PrixCameroon' }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-48 object-contain"
                             onerror="this.src='https://via.placeholder.com/300x200/10b981/ffffff?text=Image+Unavailable'">
                             
                        <div class="absolute top-2 left-2 flex flex-col gap-1">
                            @if($vendor->verified_at)
                                <span class="flex items-start benefit-card">
                                    <i class="fas fa-check-circle text-emerald-600 mt-1 mr-3 text-lg"></i>
                                </span>
                            @endif
                            @if(isset($product->is_on_sale) && $product->is_on_sale)
                                <span class="inline-flex items-center bg-red-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-lg">
                                    <i class="fas fa-fire mr-1"></i> Sale
                                </span>
                            @endif
                        </div>

                        @auth
                            <!-- If user is logged in, clicking the heart redirects them to the product details page -->
                            <a href="{{ route('product.show', $product->id) }}" 
                               class="absolute top-2 right-2 bg-white/90 hover:bg-white p-2 rounded-full shadow-md transition z-20"
                               title="View Product Details">
                                <i class="far fa-heart text-gray-600 hover:text-red-500"></i>
                            </a>
                        @else
                            <!-- If user is a guest, clicking the heart opens your login/register modal -->
                            <button type="button" 
                                    onclick="openAuthModal()" 
                                    class="absolute top-2 right-2 bg-white/90 hover:bg-white p-2 rounded-full shadow-md transition z-20"
                                    title="Sign in to save">
                                <i class="far fa-heart text-gray-600 hover:text-red-500"></i>
                            </button>
                        @endauth
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                                    {{ $product->category ?? 'General' }}
                                </span>
                                <div class="flex items-center">
                                    @php $avg = round($product->averageRating() * 2) / 2; @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $avg) <i class="fas fa-star text-amber-400 text-xs"></i>
                                        @elseif($i - 0.5 == $avg) <i class="fas fa-star-half text-amber-400 text-xs"></i>
                                        @else <i class="far fa-star text-gray-300 text-xs"></i> @endif
                                    @endfor
                                    <span class="text-[10px] text-gray-400 ml-1">({{ $product->ratings()->count() }})</span>
                                </div>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg leading-tight mb-2">{{ $product->name }}</h3>
                            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                <i class="fas fa-store"></i> <span>{{ $vendor->name }}</span>
                                @if(isset($vendor->location))
                                    <span class="text-xs">•</span> <span><i class="fas fa-map-marker-alt"></i> {{ $vendor->location }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
@php
    // Directly pull the exact row from the database table for this card
    $pivotRow = \DB::table('product_vendor')
        ->where('vendor_id', $vendor->id)
        ->where('product_id', $product->id)
        ->first();
@endphp

<div>
    <p class="text-[10px] font-bold uppercase text-gray-400">Price</p>
    <div class="flex items-baseline gap-2">
        {{-- Display strike-through if an old_price exists and is different from the current price --}}
        @if($pivotRow && !empty($pivotRow->old_price) && $pivotRow->old_price != $pivotRow->price)
            <span class="text-gray-400 line-through text-xs font-semibold">
                {{ number_format($pivotRow->old_price) }}
            </span>
        @endif
        <p class="text-xl font-black text-emerald-600">
            {{ number_format($pivotRow->price ?? $vendor->pivot->price) }} <span class="text-xs font-normal text-gray-400">FCFA</span>
        </p>
    </div>
    <p class="text-[10px] text-gray-500">
        Updated: {{ $pivotRow && $pivotRow->updated_at ? \Carbon\Carbon::parse($pivotRow->updated_at)->diffForHumans() : 'N/A' }}
    </p>
</div>
<a href="{{ route('product.show', $product->id) }}" 
   class="bg-gray-900 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl transition text-sm">
    View Details
</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
    @if(method_exists($products, 'links'))
        <div class="mt-8 flex justify-center">{{ $products->links() }}</div>
    @endif
@endif
    <!-- EMPTY STATE -->
    @if((!isset($trips) || $trips->isEmpty()) && (!isset($products) || $products->isEmpty()))
        <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
            <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No results found matching your criteria.</p>
            <a href="{{ route('vendor.register.form') }}" class="text-emerald-600 hover:underline mt-2 inline-block">Be the first vendor →</a>
        </div>
    @endif
</main>
<!-- Auth Required Modal -->
<div id="authConfirmationModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-xl border border-gray-100" id="authModalContainer">
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl">
            <i class="fas fa-heart"></i>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 text-center mb-2">Account Required</h3>
        <p class="text-gray-500 text-sm text-center mb-6">Please log in or create an account to save items to your wishlist.</p>
        
        <div class="flex flex-col gap-2.5">
            <a href="{{ url('register') }}" class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-md text-center text-sm">
                Log In / Register
            </a>
            <button type="button" onclick="closeAuthModal()" class="text-gray-400 hover:text-gray-600 text-xs font-semibold py-2 transition mt-1">
                Cancel
            </button>
        </div>
    </div>
</div>

    <!-- ===== CALL TO ACTION ===== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-2xl p-8 md:p-12 text-center text-white">
            <h2 class="text-2xl md:text-3xl font-extrabold mb-4">Ready to Start Selling?</h2>
            <p class="text-emerald-100 mb-6 max-w-2xl mx-auto">
                Join thousands of vendors and showcase your products to customers across Cameroon.
            </p>
            <a href="{{ route('vendor.register.form') }}" class="inline-block bg-white text-emerald-700 font-bold px-8 py-3 rounded-xl hover:bg-gray-100 transition shadow-lg">
                <i class="fas fa-store mr-2"></i> Register Your Shop Now
            </a>
        </div>
    </section>

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
                        <li><a href="{{ route('vendor.register.form') }}" class="hover:text-emerald-600 transition">Register Business</a></li>
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
                &copy; {{ date('Y') }} PriceCheckCameroon. All rights reserved.
            </div>
        </div>
    </footer>
   <script>
     function handleWishlistClick(isLoggedIn, loginUrl, registerUrl, productId, buttonElement) {
        if (!isLoggedIn) {
            openAuthModal();
            return;
        }

        // If logged in, send an AJAX request to toggle the wishlist
        fetch(`/wishlist/toggle/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const icon = buttonElement.querySelector('i');
            if (data.status === 'added') {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-red-500');
            } else {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
            }
        })
        .catch(error => console.error('Error:', error));
     }

     function openAuthModal() {
        const modal = document.getElementById('authConfirmationModal');
        const container = document.getElementById('authModalContainer');
        if (modal && container) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
     }

     function closeAuthModal() {
        const modal = document.getElementById('authConfirmationModal');
        const container = document.getElementById('authModalContainer');
        if (modal && container) {
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
     }
    </script>

</body>
</html>