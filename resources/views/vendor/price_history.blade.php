<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price History - {{ $product->name }} | PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">

    <!-- Navigation Bar -->
    <header class="bg-teal-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('product.search') }}" class="text-xl font-bold tracking-tight text-white">
                    PriceCheck<span class="text-amber-400">Cameroon</span> Workspace
                </a>
                @if(isset($vendor) && $vendor->name)
                    <span class="hidden md:inline-block text-xs bg-teal-900/60 text-teal-200 border border-teal-700/60 px-3 py-1 rounded-full font-medium">
                        <i class="fas fa-store mr-1.5 text-amber-400"></i> {{ $vendor->name }}
                    </span>
                @endif
            </div>
            <div>
                <a href="{{ route('vendor.dashboard') }}" class="bg-teal-900/40 hover:bg-teal-700/80 text-teal-100 hover:text-white px-3.5 py-2 rounded-xl border border-teal-700/50 text-xs font-semibold transition flex items-center space-x-1.5">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 py-10 flex-1 w-full">
        
        <!-- Header Info Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center flex-shrink-0 border border-teal-100 shadow-inner">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 bg-teal-50 text-teal-700 rounded-md">
                        {{ $product->category ?? 'General Product' }}
                    </span>
                    <h1 class="text-xl font-extrabold text-gray-900 mt-1">{{ $product->name }}</h1>
                    <p class="text-xs text-gray-500">Chronological price adjustments log.</p>
                </div>
            </div>

            @if(!$histories->isEmpty())
                <div class="bg-gray-50 px-4 py-3 rounded-xl border border-gray-100 text-right md:text-left">
                    <span class="block text-[10px] uppercase tracking-wider text-gray-400 font-bold">Total Records</span>
                    <span class="text-sm font-extrabold text-gray-800">{{ $histories->count() }} Updates</span>
                </div>
            @endif
        </div>

        <!-- History Table Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($histories->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="fas fa-history text-2xl"></i>
                    </div>
                    <h3 class="text-gray-800 font-bold text-base mb-1">No Price History Found</h3>
                    <p class="text-gray-400 text-xs max-w-sm mx-auto">No price changes have been logged for this product yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/75 text-[11px] uppercase text-gray-400 font-bold tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Date Recorded</th>
                                <th class="px-6 py-4">Price Change</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
    @php 
        // If you store an initial price or want to default the very first "old price" 
        // to something specific, you can define it here. Otherwise, we can capture 
        // the state before the first history log if available.
        $previousPrice = $product->original_price ?? null; // Adjust if you have an initial price column, or leave null for the first item
    @endphp

    @foreach($histories as $index => $history)
        <tr class="hover:bg-gray-50/65 transition group">
            <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-calendar-alt text-teal-500 text-xs"></i>
                    <span class="font-bold text-gray-900">{{ $history->created_at->format('d M, Y') }}</span>
                    <span class="text-gray-400 text-xs font-normal bg-gray-100 px-2 py-0.5 rounded-md ml-1">{{ $history->created_at->format('H:i') }}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-baseline gap-2">
                    {{-- Show the struck-through old price if a previous price exists --}}
                    @if($previousPrice !== null && $previousPrice != $history->price)
                        <span class="text-gray-400 line-through text-xs font-semibold">
                            {{ number_format($previousPrice) }}
                        </span>
                    @endif

                    {{-- Show the new history price --}}
                    <p class="text-xl font-black text-emerald-600 tracking-tight">
                        {{ number_format($history->price) }} <span class="text-xs font-normal text-gray-400 uppercase">FCFA</span>
                    </p>
                </div>
            </td>
        </tr>
        @php 
            // Set the current price as the $previousPrice for the next row in the loop
            $previousPrice = $history->price; 
        @endphp
    @endforeach
</tbody>
                    </table>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-6 text-xs text-gray-400 border-t border-gray-100 bg-white mt-12">
        <p>&copy; {{ date('Y') }} PriceCheckCameroon Workspace. All rights reserved.</p>
    </footer>

</body>
</html>