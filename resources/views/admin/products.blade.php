<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">All Tracked Products</h2>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">Product Name</th>
                            <th class="p-4">Vendor Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 font-medium text-gray-800">{{ $product->name }}</td>
                            <td class="p-4">
                                <!-- Clickable Badge to Open Modal -->
                                <button type="button" onclick="openProductModal('modal-product-{{ $product->id }}')" class="bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition px-3 py-1 rounded-full text-xs font-bold cursor-pointer inline-flex items-center">
                                    {{ $product->vendors_count }} Shops <i class="fas fa-external-link-alt ml-1"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODALS OUTSIDE THE TABLE -->
    @foreach($products as $product)
    <div id="modal-product-{{ $product->id }}" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-6">
            <div class="flex justify-between items-center border-b pb-4">
                <h3 class="text-xl font-bold text-gray-900">{{ $product->name }} - Selling Vendors</h3>
                <button type="button" onclick="closeProductModal('modal-product-{{ $product->id }}')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <!-- Vendors Table inside Modal -->
            <div>
                @if($product->vendors && $product->vendors->count() > 0)
                    <div class="max-h-64 overflow-y-auto border rounded-lg">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b sticky top-0">
                                <tr>
                                    <th class="p-3 font-semibold text-gray-600">Vendor Name</th>
                                    <th class="p-3 font-semibold text-gray-600">Location</th>
                                    <th class="p-3 font-semibold text-gray-600">Listed Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->vendors as $vendor)
                                    <tr class="border-b hover:bg-gray-50 last:border-b-0">
                                        <td class="p-3 font-medium text-gray-800">
                                            <i class="fas fa-store text-gray-400 mr-2"></i>{{ $vendor->name }}
                                        </td>
                                        <td class="p-3 text-gray-600">{{ $vendor->location ?? 'N/A' }}</td>
                                        <td class="p-3 font-bold text-emerald-600">
                                            {{ number_format($vendor->pivot->price ?? 0, 0) }} XAF
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic bg-gray-50 p-4 rounded-xl text-center">No vendors are currently listed for this product.</p>
                @endif
            </div>

            <div class="border-t pt-4 flex justify-end">
                <button type="button" onclick="closeProductModal('modal-product-{{ $product->id }}')" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-700 transition">Close</button>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Script to toggle Modals -->
    <script>
        function openProductModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        function closeProductModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</body>
</html>