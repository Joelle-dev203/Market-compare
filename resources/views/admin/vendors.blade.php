<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vendors | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet CSS for Map inside Modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .leaflet-control-attribution { display: none !important; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-gray-900 text-white p-6 flex flex-col">
            <h1 class="text-2xl font-bold mb-10 text-emerald-400">PrixCam Admin</h1>
            <nav class="space-y-4 flex-1">
    <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-emerald-400' : 'hover:bg-gray-800' }} font-bold">
        <i class="fas fa-chart-pie mr-2"></i> Dashboard
    </a>
    <a href="{{ route('admin.vendors') }}" class="block py-2.5 px-4 rounded {{ request()->routeIs('admin.vendors') ? 'bg-gray-800 text-emerald-400' : 'hover:bg-gray-800' }}">
        <i class="fas fa-store-alt mr-2"></i> Vendors
    </a>
    <a href="{{ route('admin.products') }}" class="block py-2.5 px-4 rounded {{ request()->routeIs('admin.products') ? 'bg-gray-800 text-emerald-400' : 'hover:bg-gray-800' }}">
        <i class="fas fa-boxes mr-2"></i> Products
    </a>
    <a href="{{ route('admin.agencies') }}" class="block py-2.5 px-4 rounded {{ request()->routeIs('admin.agencies') ? 'bg-gray-800 text-emerald-400' : 'hover:bg-gray-800' }}">
        <i class="fas fa-route mr-2"></i> Agencies
    </a>
</nav>
        </aside>

        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Registered Vendors</h2>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                        <tr class="border-b hover:bg-gray-50">
                            <!-- Clickable Name triggers Modal -->
                            <td class="p-4">
                                <button onclick="openVendorModal('modal-{{ $vendor->id }}')" class="text-emerald-600 font-bold hover:underline text-left">
                                    {{ $vendor->name }}
                                </button>
                            </td>
                            <td class="p-4 text-gray-700 font-medium">{{ $vendor->category ?? 'General' }}</td>
                            <td class="p-4">
                                <span class="{{ $vendor->is_approved ? 'text-emerald-600' : 'text-amber-600' }} font-bold">
                                    {{ $vendor->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            <td class="p-4 flex items-center gap-2 flex-wrap">
                                <!-- Verify Action -->
                                @if(!$vendor->verified_at)
                                    <form action="{{ route('admin.verify-vendor', $vendor->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs bg-emerald-600 text-white px-3 py-1 rounded hover:bg-emerald-700 transition">
                                            Verify
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-emerald-600 font-bold">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                @endif

                                <!-- Deactivate / Activate Button -->
                                <form action="{{ route('admin.toggle-vendor-status', $vendor->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs {{ $vendor->is_approved ? 'bg-amber-500 hover:bg-amber-600' : 'bg-blue-500 hover:bg-blue-600' }} text-white px-3 py-1 rounded transition">
                                        {{ $vendor->is_approved ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <!-- Delete Button with Confirmation -->
                                <form action="{{ route('admin.reject-vendor', $vendor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vendor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- MODAL FOR EACH VENDOR -->
                        <div id="modal-{{ $vendor->id }}" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
                            <div class="bg-white rounded-2xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-6">
                                <div class="flex justify-between items-center border-b pb-4">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $vendor->name }} - Profile & Products</h3>
                                    <button onclick="closeVendorModal('modal-{{ $vendor->id }}')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
                                </div>

                                <!-- Vendor Info Grid -->
                                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl text-sm">
                                    <div>
                                        <span class="text-xs text-gray-400 uppercase font-bold block">Category</span>
                                        <p class="font-medium text-gray-800">{{ $vendor->category ?? 'General' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 uppercase font-bold block">Phone</span>
                                        <p class="font-medium text-gray-800">{{ $vendor->phone_number ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 uppercase font-bold block">Location</span>
                                        <p class="font-medium text-emerald-600">{{ $vendor->location ?? 'Douala, Cameroon' }}</p>
                                    </div>
                                </div>

                                <!-- Product Cards Section with Internal Scrolling -->
                                <div>
                                    <h4 class="font-bold text-gray-800 mb-3">Products Listed By This Vendor</h4>
                                    
                                    @if($vendor->products && $vendor->products->count() > 0)
                                        <!-- Scrollable container for many products -->
                                        <div class="max-h-60 overflow-y-auto pr-2 space-y-3">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                @foreach($vendor->products as $product)
                                                    <div class="border rounded-xl p-3 flex gap-3 items-center bg-white shadow-sm">
                                                        
                                                        <!-- Product Image (From main Product table) -->
                                                        @if(!empty($product->image_url))
                                                            @php
                                                                $cleanPath = str_replace('storage/app/public/', 'storage/', $product->image_url);
                                                            @endphp
                                                            <img src="{{ asset($cleanPath) }}" alt="Product Image" class="w-14 h-14 object-cover rounded-lg flex-shrink-0 border bg-gray-50">
                                                        @else
                                                            <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 flex-shrink-0">
                                                                <i class="fas fa-box text-lg"></i>
                                                            </div>
                                                        @endif

                                                        <!-- Product Info (Price from pivot table) -->
                                                        <div class="min-w-0 flex-1">
                                                            <h5 class="font-bold text-sm text-gray-900 truncate">{{ $product->name }}</h5>
                                                            <p class="text-emerald-600 font-bold text-xs mt-1">
                                                                {{ number_format($product->pivot->price ?? 0, 0) }} XAF
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic bg-gray-50 p-4 rounded-xl text-center">No products uploaded by this vendor yet.</p>
                                    @endif
                                </div>

                                <div class="border-t pt-4 flex justify-end">
                                    <button onclick="closeVendorModal('modal-{{ $vendor->id }}')" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-700 transition">Close</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Script to toggle Modals -->
    <script>
        function openVendorModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        function closeVendorModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</body>
</html>