<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $trip->name ?? 'Trip Details' }} | PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .toast-animate { animation: slideUp 0.5s ease-out; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .rating-star { transition: all 0.2s ease; }
        .rating-star:hover { transform: scale(1.2); }
        [x-cloak] { display: none !important; }
        .trip-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .trip-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
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

    <!-- ===== CATEGORY NAV ===== -->
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
                        'Clothing' => ['Men', 'Women', 'Shoes', 'Handbags'],
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
<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10" 
      x-data="{ 
          selectedTripType: 'One-Way', 
          selectedDateSlot: '',
          selectedClass: '',
          selectedPrice: '0',
          selectedSeatFeature: '',
          selectedStopInfo: '',
          selectedReturnStopInfo: '',
          selectedOutboundArrival: '',
          selectedReturnDeparture: '',
          selectedReturnArrival: ''
      }"
      x-init="
          @php
              $rawPrices = is_string($trip->prices) ? json_decode($trip->prices, true) : ($trip->prices ?? []);
              
              // Detect transport type directly from the database column
              $isBus = ($trip->type ?? '') === 'bus';

              $defaultClass = array_key_first($rawPrices);
              $defaultPriceData = $rawPrices[$defaultClass] ?? [];
              
              if (is_array($defaultPriceData)) {
                  $defaultOneWay = $defaultPriceData['one_way'] ?? $defaultPriceData['price'] ?? 0;
                  $defaultSeatFeature = $defaultPriceData['seat_feature'] ?? $defaultPriceData['feature'] ?? $defaultPriceData['seat'] ?? $defaultPriceData['class_feature'] ?? 'Standard Seat';
              } else {
                  $defaultOneWay = $defaultPriceData;
                  $defaultSeatFeature = 'Standard Seat';
              }

              $rawSchedules = is_string($trip->schedules) ? json_decode($trip->schedules, true) : ($trip->schedules ?? []);
              $firstSlot = $rawSchedules[0] ?? [];
              
              $defaultDep = is_array($firstSlot) ? ($firstSlot['departure'] ?? '') : $firstSlot;
              $defaultArr = is_array($firstSlot) ? ($firstSlot['arrival'] ?? '') : '';
              
              $defaultStop = is_array($firstSlot) ? ($firstSlot['stop'] ?? $firstSlot['stop_information'] ?? $firstSlot['layover'] ?? $firstSlot['stops'] ?? $firstSlot['outbound_stop'] ?? $firstSlot['outbound_layover'] ?? $firstSlot['outbound_stops'] ?? '') : '';
              if (empty($defaultStop) && is_array($firstSlot) && isset($firstSlot['stop_list']) && is_array($firstSlot['stop_list'])) {
                  $defaultStop = $firstSlot['stop_list'][0] ?? '';
              }
              
              $defaultRetDep = is_array($firstSlot) ? ($firstSlot['return_departure'] ?? '') : '';
              $defaultRetArr = is_array($firstSlot) ? ($firstSlot['return_arrival'] ?? '') : '';
              
              $defaultRetStop = is_array($firstSlot) ? ($firstSlot['stop'] ?? $firstSlot['stop_information'] ?? $firstSlot['layover'] ?? $firstSlot['stops'] ?? $firstSlot['return_stop'] ?? $firstSlot['return_layover'] ?? $firstSlot['return_stops'] ?? '') : '';
              if (empty($defaultRetStop) && is_array($firstSlot) && isset($firstSlot['stop_list']) && is_array($firstSlot['stop_list'])) {
                  $defaultRetStop = $firstSlot['stop_list'][0] ?? '';
              }
          @endphp
          selectedClass = '{{ $defaultClass }}';
          selectedPrice = '{{ $defaultOneWay }}';
          selectedSeatFeature = '{{ $defaultSeatFeature }}';
          selectedDateSlot = '{{ $defaultDep }}';
          selectedOutboundArrival = '{{ $defaultArr }}';
          selectedStopInfo = '{{ $defaultStop }}';
          selectedReturnDeparture = '{{ $defaultRetDep }}';
          selectedReturnArrival = '{{ $defaultRetArr }}';
          selectedReturnStopInfo = '{{ $defaultRetStop }}';
      ">

    <!-- Back Button -->
    <a href="{{ route('product.search') }}" class="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 transition mb-6 group">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition"></i> Back to Search
    </a>

    <!-- ===== TRIP HEADER ===== -->
    <div class="relative bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row items-center gap-8">
        
        <!-- Wishlist & Alert Buttons -->
        @auth
        @php
            $isWishlisted = Auth::user()?->wishlists?->contains('product_id', $trip->id) ?? false;
        @endphp
        <div class="absolute top-6 right-6 flex flex-col gap-3 z-10">
            <button id="wishlist-btn" data-id="{{ $trip->id }}" class="flex items-center gap-2 font-bold text-sm hover:opacity-75">
                <i id="wishlist-icon" class="{{ $isWishlisted ? 'fas text-red-500' : 'far text-gray-400' }} fa-heart transition-all"></i>
                <span id="wishlist-text" class="text-gray-600">{{ $isWishlisted ? 'Wishlisted' : 'Save' }}</span>
            </button>

            <button id="alert-btn" data-id="{{ $trip->id }}" class="flex items-center gap-2 font-bold text-sm text-emerald-600 hover:opacity-75">
                <i class="fas fa-bell text-amber-500"></i>
                <span id="alert-text">Set Alert</span>
            </button>
        </div>
        @endauth

        <!-- Agency Logo -->
        <div class="flex-shrink-0">
            <img src="{{ $trip->agency->logo_path ? asset('storage/' . $trip->agency->logo_path) : 'https://via.placeholder.com/300x200/10b981/ffffff?text=Agency' }}" 
                 alt="{{ $trip->agency->name }}" 
                 class="w-48 h-36 object-cover rounded-2xl border border-gray-100 shadow-inner">
        </div>

        <!-- Trip Details -->
        <div class="flex-1 text-center md:text-left w-full">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                    {{ $trip->type ?? ($isBus ? 'Bus Trip' : 'Flight') }}
                </span>
                @if($trip->is_featured ?? false)
                    <span class="inline-flex items-center bg-amber-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        <i class="fas fa-star mr-1"></i> Featured
                    </span>
                @endif
            </div>

            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $trip->name }}</h1>
            
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mt-2 text-sm text-gray-500">
                <span class="flex items-center"><i class="{{ $isBus ? 'fas fa-bus' : 'fas fa-plane' }} text-emerald-500 mr-1"></i> {{ $trip->agency->name }}</span>
                @if($trip->agency->verified_at)
                    <span class="text-emerald-500"><i class="fas fa-check-circle"></i></span>
                @endif
            </div>

            <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mt-4">
                <div class="text-center md:text-left">
                    <p class="font-bold text-gray-400 uppercase text-xs">Origin</p>
                    <p class="font-bold text-lg text-emerald-700">{{ $trip->route->departure_city ?? 'N/A' }}</p>
                </div>
                <div class="text-emerald-500 text-xl font-bold"><i class="fas fa-long-arrow-alt-right"></i></div>
                <div class="text-center md:text-left">
                    <p class="font-bold text-gray-400 uppercase text-xs">Destination</p>
                    <p class="font-bold text-lg text-emerald-700">{{ $trip->route->arrival_city ?? 'N/A' }}</p>
                </div>
            </div>

 <!-- Rating Section for Trips -->
<div class="mt-6 border-t pt-4">
    @auth
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
            {{ ($userRating ?? 0) > 0 ? 'Your Rating' : 'Rate this Trip' }}
        </h4>
        <div class="flex items-center gap-3">
            <form action="{{ route('trip.rate', $trip->id) }}" method="POST" class="flex items-center gap-1">
                @csrf
                @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer rating-star">
                        <input type="radio" name="rating" value="{{ $i }}" class="hidden" onchange="this.form.submit()">
                        <i class="fas fa-star text-xl {{ ($userRating ?? 0) >= $i ? 'text-amber-400' : 'text-gray-300 hover:text-amber-400' }}"></i>
                    </label>
                @endfor
            </form>

            @if(($userRating ?? 0) > 0)
                <form action="{{ route('trip.rate', $trip->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="remove" value="1">
                    <button type="submit" class="text-xs text-red-500 hover:underline font-bold">Clear</button>
                </form>
            @endif
        </div>
    @endauth
</div>
        </div>
    </div>

    {{-- Notification Toast --}}
    <div id="toast" class="hidden fixed bottom-5 right-5 bg-gray-900 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 z-50">
        <i class="fas fa-bell text-amber-400"></i>
        <span id="toast-message"></span>
    </div>

    <!-- ========================================== -->
    <!-- FLIGHT-SPECIFIC INTERACTIVE FILTERS        -->
    <!-- ========================================== -->
    @if(!$isBus)
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row gap-6 items-center justify-between">
        <div class="w-full md:w-1/2">
            <label class="block text-xs font-black uppercase tracking-wider text-gray-500 mb-2">
                <i class="fas fa-exchange-alt text-emerald-500 mr-1"></i> Select Trip Type
            </label>
            <select x-model="selectedTripType" 
                    @change="
                        let activeCard = document.querySelector('[data-class-name=\x27' + selectedClass + '\x27]');
                        if(activeCard) {
                            selectedPrice = selectedTripType === 'One-Way' ? activeCard.dataset.oneWay : activeCard.dataset.roundTrip;
                            selectedSeatFeature = activeCard.dataset.seatFeature || 'Standard Seat';
                        }
                    "
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-gray-800 focus:outline-none focus:border-emerald-500 transition">
                <option value="One-Way">One-Way</option>
                <option value="Round-Trip">Round-Trip</option>
            </select>
        </div>

        <div class="w-full md:w-1/2">
            <label class="block text-xs font-black uppercase tracking-wider text-gray-500 mb-2">
                <i class="fas fa-calendar-alt text-emerald-500 mr-1"></i> Select Departure Date & Time Slot
            </label>
            <select @change="
                let selectedOpt = $el.options[$el.selectedIndex];
                selectedDateSlot = selectedOpt.value;
                selectedOutboundArrival = selectedOpt.dataset.arrival || '';
                selectedStopInfo = selectedOpt.dataset.stop || '';
                selectedReturnDeparture = selectedOpt.dataset.returnDeparture || '';
                selectedReturnArrival = selectedOpt.dataset.returnArrival || '';
                selectedReturnStopInfo = selectedOpt.dataset.returnStop || '';
            " class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-gray-800 focus:outline-none focus:border-emerald-500 transition">
                @php
                    $parsedSchedules = is_string($trip->schedules) ? json_decode($trip->schedules, true) : ($trip->schedules ?? []);
                @endphp
                @forelse($parsedSchedules as $slot)
                    @php
                        $dep = is_array($slot) ? ($slot['departure'] ?? '') : $slot;
                        $arr = is_array($slot) ? ($slot['arrival'] ?? '') : '';
                        
                        $st = '';
                        if (is_array($slot)) {
                            $st = $slot['stop'] ?? $slot['stop_information'] ?? $slot['layover'] ?? $slot['stops'] ?? $slot['outbound_stop'] ?? $slot['outbound_layover'] ?? $slot['outbound_stops'] ?? '';
                            if (empty($st) && isset($slot['stop_list']) && is_array($slot['stop_list'])) {
                                $st = $slot['stop_list'][0] ?? '';
                            }
                        }

                        $retDep = is_array($slot) ? ($slot['return_departure'] ?? '') : '';
                        $retArr = is_array($slot) ? ($slot['return_arrival'] ?? '') : '';
                        
                        $retSt = '';
                        if (is_array($slot)) {
                            $retSt = $slot['stop'] ?? $slot['stop_information'] ?? $slot['layover'] ?? $slot['stops'] ?? $slot['return_stop'] ?? $slot['return_layover'] ?? $slot['return_stops'] ?? '';
                            if (empty($retSt) && isset($slot['stop_list']) && is_array($slot['stop_list'])) {
                                $retSt = $slot['stop_list'][0] ?? '';
                            }
                        }
                    @endphp
                    <option value="{{ $dep }}" 
                            data-arrival="{{ $arr }}" 
                            data-stop="{{ $st }}" 
                            data-return-departure="{{ $retDep }}" 
                            data-return-arrival="{{ $retArr }}" 
                            data-return-stop="{{ $retSt }}">
                        Departure: {{ $dep }} @if($arr) (Arrival: {{ $arr }}) @endif @if($st) [Stop: {{ $st }}] @endif
                    </option>
                @empty
                    <option value="">No schedules available in database</option>
                @endforelse
            </select>
        </div>
    </div>
    @endif
    <!-- End Flight-Specific Interactive Filters -->

    <!-- ========================================== -->
    <!-- CLASSES & PRICING SELECTION (Bus & Flight) -->
    <!-- ========================================== -->
    <div class="space-y-6 mb-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-emerald-600 rounded-full"></div>
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-ticket-alt text-emerald-500 mr-2"></i> Select {{ $isBus ? 'Bus Class & Price' : 'Flight Class & Price' }}
                </h2>
            </div>
            @if(!$isBus)
                <div class="text-xs font-bold bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-xl">
                    Showing prices for: <span x-text="selectedTripType" class="underline"></span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($rawPrices as $className => $priceData)
            @php
                $oneWayPrice = 0;
                $roundTripPrice = 0;
                $seatFeature = 'Standard Seat';
                $scheduleTimes = [];

                if (is_array($priceData)) {
                    $oneWayPrice = $priceData['one_way'] ?? $priceData['price'] ?? 0;
                    $roundTripPrice = $priceData['round_trip'] ?? ($oneWayPrice * 2);
                    
                    if (isset($priceData['times']) && is_array($priceData['times'])) {
                        $scheduleTimes = $priceData['times'];
                    } elseif (isset($priceData['time'])) {
                        $scheduleTimes = [$priceData['time']];
                    }

                    if (isset($priceData['features']) && is_array($priceData['features'])) {
                        $seatFeature = implode(', ', $priceData['features']);
                    } else {
                        $seatFeature = $priceData['seat_feature'] ?? $priceData['feature'] ?? $priceData['seat'] ?? $priceData['class_feature'] ?? $priceData['details'] ?? $priceData['attributes'] ?? 'Standard Seat';
                    }
                } else {
                    $oneWayPrice = $priceData;
                    $roundTripPrice = $priceData * 2;
                }
            @endphp
            <div data-class-name="{{ $className }}"
                 data-one-way="{{ $oneWayPrice }}"
                 data-round-trip="{{ $roundTripPrice }}"
                 data-seat-feature="{{ $seatFeature }}"
                 @click="
                    selectedClass = '{{ $className }}'; 
                    selectedPrice = {{ $isBus ? $oneWayPrice : "selectedTripType === 'One-Way' ? '{$oneWayPrice}' : '{$roundTripPrice}'" }}; 
                    selectedSeatFeature = '{{ $seatFeature }}';
                 "
                 class="bg-white p-6 rounded-2xl shadow-sm border-2 transition cursor-pointer flex flex-col justify-between"
                 :class="selectedClass === '{{ $className }}' ? 'border-emerald-600 bg-emerald-50/20 shadow-md' : 'border-gray-100 hover:border-gray-200'">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-extrabold text-gray-900 text-lg uppercase tracking-wider text-emerald-700">{{ $className }}</h3>
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition"
                             :class="selectedClass === '{{ $className }}' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300'">
                            <i class="fas fa-check text-xs" x-show="selectedClass === '{{ $className }}'"></i>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-4">
                        @if($isBus && !empty($scheduleTimes))
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-[11px] font-bold uppercase text-gray-400 block mb-1">Available Hours</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($scheduleTimes as $timeSlot)
                                        <span class="bg-white border border-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded shadow-sm">🕒 {{ $timeSlot }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-center">
                            @if(!$isBus)
                                <span class="text-xs font-bold uppercase text-gray-400 block mb-1" x-text="selectedTripType + ' Price'"></span>
                            @else
                                <span class="text-xs font-bold uppercase text-gray-400 block mb-1">Price</span>
                            @endif
                            <span class="text-2xl font-black text-emerald-700">
                                FCFA <span x-text="Number({{ $isBus ? $oneWayPrice : "selectedTripType === 'One-Way' ? {$oneWayPrice} : {$roundTripPrice}" }}).toLocaleString()"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-xs font-bold text-center text-emerald-600 uppercase tracking-wider py-1" x-text="selectedClass === '{{ $className }}' ? '✓ Selected' : 'Click to select'"></div>
            </div>
            @empty
            <p class="text-gray-500 italic">No classes listed for this trip.</p>
            @endforelse
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCHEDULE & DETAILED ROUTE SPECIFICATIONS   -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        
        <!-- Left: Unified Single Booking Card -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl shadow-lg text-white sticky top-28">
                <h2 class="text-sm font-bold uppercase text-gray-400 mb-2 flex items-center">
                    <i class="fas fa-ticket-alt mr-2 text-emerald-400"></i> Booking Summary
                </h2>
                <div class="space-y-3 mb-6 text-sm border-y border-gray-700 py-4">
                    @if(!$isBus)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Trip Type:</span>
                            <span class="font-bold" x-text="selectedTripType"></span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-400">Class:</span>
                        <span class="font-bold text-emerald-400" x-text="selectedClass"></span>
                    </div>
                    @if(!$isBus)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Seat Feature:</span>
                            <span class="font-bold text-amber-300 text-right" x-text="selectedSeatFeature"></span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-400">Price:</span>
                        <span class="font-bold">FCFA <span x-text="Number(selectedPrice).toLocaleString()"></span></span>
                    </div>
                </div>
                
                @if($isBus)
                    <a :href="'https://wa.me/{{ $trip->agency->phone_number ?? '#' }}?text=' + encodeURIComponent('Hello, I want to book a bus ticket (' + selectedClass + ') for route: {{ $trip->route->departure_city ?? '' }} to {{ $trip->route->arrival_city ?? '' }}')" target="_blank"
                        class="block w-full bg-emerald-600 hover:bg-emerald-700 text-center py-3.5 rounded-xl font-bold transition shadow-lg hover:shadow-xl uppercase tracking-wider text-sm">
                        <i class="fab fa-whatsapp mr-2 text-lg"></i> Book This Bus Now
                    </a>
                @else
                    <a :href="'https://wa.me/{{ $trip->agency->phone_number ?? '#' }}?text=' + encodeURIComponent('Hello, I want to book a ' + selectedTripType + ' flight (' + selectedClass + ' - ' + selectedSeatFeature + ') on slot: ' + selectedDateSlot)" target="_blank"
                        class="block w-full bg-emerald-600 hover:bg-emerald-700 text-center py-3.5 rounded-xl font-bold transition shadow-lg hover:shadow-xl uppercase tracking-wider text-sm">
                        <i class="fab fa-whatsapp mr-2 text-lg"></i> Book This Flight Now
                    </a>
                @endif
                
                <div class="text-center text-xs text-gray-400 mt-3 space-y-1">
                    <p><i class="fas fa-shield-alt mr-1"></i> Prices checked recently. Verify with agency.</p>
                    @if(isset($trip->updated_at))
                        <p class="text-[11px] text-gray-400 font-medium">
                            <i class="far fa-clock mr-1"></i> Price Last Updated: {{ $trip->updated_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Detailed Specifications -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="{{ $isBus ? 'fas fa-road' : 'fas fa-route' }} text-emerald-500 mr-2"></i> {{ $isBus ? 'Bus Route Information & Details' : 'Flight Itinerary & Route Details' }}
                </h2>
                
                <div class="space-y-4 text-sm">
                    @if($isBus)
                        <!-- ========================================== -->
                        <!-- BUS ROUTE SPECIFICATIONS                   -->
                        <!-- ========================================== -->
                        <div class="p-4 bg-emerald-50 rounded-xl space-y-3 border border-emerald-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-emerald-700 font-extrabold uppercase tracking-wider">
                                    ROUTE ({{ strtoupper($trip->route->departure_city ?? 'ORIGIN') }} ➔ {{ strtoupper($trip->route->arrival_city ?? 'DESTINATION') }})
                                </span>
                                <span class="bg-emerald-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">ONE-WAY</span>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-emerald-100 space-y-3">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-bus text-emerald-600 mt-1"></i>
                                    <div>
                                        <span class="text-gray-400 text-xs block font-bold">DEPARTURE</span>
                                        <span class="font-extrabold text-gray-900">{{ $trip->route->departure_city ?? 'Origin' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <i class="fas fa-bus text-emerald-600 mt-1"></i>
                                    <div>
                                        <span class="text-gray-400 text-xs block font-bold">ARRIVAL</span>
                                        <span class="font-extrabold text-gray-900">{{ $trip->route->arrival_city ?? 'Destination' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- ========================================== -->
                        <!-- FLIGHT ITINERARY SPECIFICATIONS            -->
                        <!-- ========================================== -->
                        <!-- Outbound Leg -->
                        <div class="p-4 bg-emerald-50 rounded-xl space-y-3 border border-emerald-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-emerald-700 font-extrabold uppercase tracking-wider">Outbound Journey ({{ $trip->route->departure_city ?? 'Origin' }} ➔ {{ $trip->route->arrival_city ?? 'Destination' }})</span>
                                <span class="bg-emerald-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase" x-text="selectedTripType"></span>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-emerald-100 space-y-2">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-plane-departure text-emerald-600 mt-1"></i>
                                    <div>
                                        <span class="text-gray-400 text-xs block font-bold">DEPARTURE</span>
                                        <span class="font-extrabold text-gray-900" x-text="'{{ $trip->route->departure_city ?? 'Origin' }} - ' + selectedDateSlot"></span>
                                    </div>
                                </div>
                                
                                <template x-if="selectedStopInfo">
                                    <div class="border-l-2 border-dashed border-emerald-200 ml-2 pl-6 py-1 text-xs text-gray-500 font-medium">
                                        <i class="fas fa-map-marker-alt text-amber-500 mr-1"></i> Stop: <span x-text="selectedStopInfo"></span>
                                    </div>
                                </template>

                                <div class="flex items-start gap-3">
                                    <i class="fas fa-plane-arrival text-emerald-600 mt-1"></i>
                                    <div>
                                        <span class="text-gray-400 text-xs block font-bold">ARRIVAL</span>
                                        <span class="font-extrabold text-gray-900" x-text="'{{ $trip->route->arrival_city ?? 'Destination' }} - ' + selectedOutboundArrival"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Return Leg (Shown only if Round-Trip is selected) -->
                        <div x-show="selectedTripType === 'Round-Trip'" class="p-4 bg-amber-50 rounded-xl space-y-3 border border-amber-100" style="display: none;">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-amber-700 font-extrabold uppercase tracking-wider">Return Journey ({{ $trip->route->arrival_city ?? 'Destination' }} ➔ {{ $trip->route->departure_city ?? 'Origin' }})</span>
                                <span class="bg-amber-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">Return</span>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-amber-100 space-y-2">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-plane-departure text-amber-600 mt-1"></i>
                                    <div>
                                        <span class="text-gray-400 text-xs block font-bold">RETURN DEPARTURE</span>
                                        <span class="font-extrabold text-gray-900" x-text="'{{ $trip->route->arrival_city ?? 'Destination' }} - ' + selectedReturnDeparture"></span>
                                    </div>
                                </div>
                                
                                <template x-if="selectedReturnStopInfo">
                                    <div class="border-l-2 border-dashed border-amber-200 ml-2 pl-6 py-1 text-xs text-gray-500 font-medium">
                                        <i class="fas fa-map-marker-alt text-emerald-500 mr-1"></i> Stop: <span x-text="selectedReturnStopInfo"></span>
                                    </div>
                                </template>

                                <div class="flex items-start gap-3">
                                    <i class="fas fa-plane-arrival text-amber-600 mt-1"></i>
                                    <div>
                                        <span class="text-gray-400 text-xs block font-bold">RETURN ARRIVAL</span>
                                        <span class="font-extrabold text-gray-900" x-text="'{{ $trip->route->departure_city ?? 'Origin' }} - ' + selectedReturnArrival"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- About This Route/Flight -->
            @if(!empty($trip->description))
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle text-emerald-500 mr-2"></i> About This {{ $isBus ? 'Bus' : 'Flight' }} Route
                    </h2>
                    <p class="text-gray-600 leading-relaxed">{{ $trip->description }}</p>
                </div>
            @endif
        </div>
    </div>


    <!-- ===== OTHER TRIPS FROM THIS AGENCY ===== -->
    @if(isset($agencyTrips) && $agencyTrips->count() > 0)
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-extrabold text-gray-900">
                    <i class="fas fa-building text-emerald-500 mr-2"></i> More Trips from {{ $trip->agency->name }}
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($agencyTrips as $agencyTrip)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="h-40 overflow-hidden bg-gray-100 relative">
                                <img src="{{ $agencyTrip->agency->logo_path ? asset('storage/' . $agencyTrip->agency->logo_path) : 'https://via.placeholder.com/300x200/10b981/ffffff?text=Trip' }}" 
                                     alt="{{ $agencyTrip->name }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                    {{ $agencyTrip->type ?? 'Trip' }}
                                </span>
                                <h4 class="font-extrabold text-gray-900 text-base mt-2 mb-1">{{ $agencyTrip->name }}</h4>
                                <p class="text-xs text-gray-500 font-medium">
                                    <i class="fas fa-route text-emerald-500 mr-1"></i> {{ $agencyTrip->route->departure_city ?? 'N/A' }} ➔ {{ $agencyTrip->route->arrival_city ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <div class="text-xs font-bold text-gray-700 text-center mb-2">{{ $agencyTrip->agency->name }}</div>
                            <a href="{{ route('trips.show', $agencyTrip->id) }}" class="block w-full bg-gray-50 hover:bg-emerald-600 hover:text-white text-center py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-emerald-700 transition">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ===== SIMILAR TRIPS FROM OTHER AGENCIES ===== -->
    @if(isset($similarTrips) && $similarTrips->count() > 0)
        <div class="mb-10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-extrabold text-gray-900">
                    <i class="fas fa-exchange-alt text-emerald-500 mr-2"></i> Similar Trips from Other Agencies
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($similarTrips as $simTrip)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="h-40 overflow-hidden bg-gray-100 relative">
                                <img src="{{ $simTrip->agency->logo_path ? asset('storage/' . $simTrip->agency->logo_path) : 'https://via.placeholder.com/300x200/10b981/ffffff?text=Trip' }}" 
                                     alt="{{ $simTrip->name }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                    {{ $simTrip->type ?? 'Trip' }}
                                </span>
                                <h4 class="font-extrabold text-gray-900 text-base mt-2 mb-1">{{ $simTrip->name }}</h4>
                                <p class="text-xs text-gray-500 font-medium">
                                    <i class="fas fa-route text-emerald-500 mr-1"></i> {{ $simTrip->route->departure_city ?? 'N/A' }} ➔ {{ $simTrip->route->arrival_city ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <div class="text-xs font-bold text-gray-700 text-center mb-2">{{ $simTrip->agency->name }}</div>
                            <a href="{{ route('trips.show', $simTrip->id) }}" class="block w-full bg-gray-50 hover:bg-emerald-600 hover:text-white text-center py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-emerald-700 transition">
                                View Details
                            </a>
                        </div>
                    </div> 
                @endforeach
            </div>
        </div>
    @endif
</main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-white border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 text-lg">PrixCameroon</h4>
                    <p class="text-sm text-gray-500">Compare prices from local stores and travel agencies to save money on your trips.</p>
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
                &copy; {{ date('Y') }} PrixCameroon. All rights reserved.
            </div>
        </div>
    </footer>

@auth
<script>
    const csrfToken = '{{ csrf_token() }}';
    const tripId = '{{ $trip->id }}';

    // Wishlist Toggle
    document.getElementById('wishlist-btn')?.addEventListener('click', function() {
        let icon = document.getElementById('wishlist-icon');
        let text = document.getElementById('wishlist-text');
        fetch(`/wishlist/trip/toggle/${tripId}`, {
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
    document.getElementById('alert-btn')?.addEventListener('click', function() {
        fetch(`/price-alerts/trip/toggle/${tripId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
        }).then(() => {
            showToast('Alert toggled! You will be emailed if the price drops.');
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
</body>
</html>