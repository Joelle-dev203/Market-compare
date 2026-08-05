<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex">
        <aside class="w-64 bg-gray-900 text-white p-6 flex flex-col">
            <h1 class="text-2xl font-bold mb-10 text-emerald-400">PriceCheck CAM</h1>
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

            <form method="POST" action="{{ route('admin.logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="w-full text-left py-2.5 px-4 rounded hover:bg-red-900 text-gray-400 hover:text-white transition">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h2>

            <!-- Flash Message Banner -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Stats Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <p class="text-gray-500 text-sm">Total Vendors</p>
        <p class="text-3xl font-bold text-gray-900">{{ $totalVendors }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <p class="text-gray-500 text-sm">Total Agencies</p>
        <p class="text-3xl font-bold text-gray-900">{{ $totalAgencies }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <p class="text-gray-500 text-sm">Total Products Tracked</p>
        <p class="text-3xl font-bold text-gray-900">{{ $totalProducts }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex justify-between items-center">
        <div>
            <p class="text-gray-500 text-sm">Total Users</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="fas fa-users"></i>
        </div>
    </div>
</div>

            <!-- Approvals Section Side-by-Side -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Pending Vendors -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center justify-between">
                        <span>Pending Vendor Approvals</span>
                        <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-1 rounded-full">{{ count($pendingVendors) }}</span>
                    </h3>
                    
                    @forelse($pendingVendors as $vendor)
                        <div class="flex justify-between items-center py-4 border-b border-gray-100 last:border-0">
                            <div>
                                <!-- Clickable link to view details & map -->
                                <a href="{{ route('admin.vendor.show', $vendor->id) }}" class="font-bold text-gray-800 hover:text-emerald-600 underline">
                                    {{ $vendor->name }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $vendor->email }}</p>
                                <!-- <p class="text-xs text-emerald-600 font-medium"><i class="fas fa-map-marker-alt mr-1"></i> {{ $vendor->market_name ?? 'Location check required' }}</p> -->
                            </div>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.approve-vendor', $vendor->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-700 transition">Approve</button>
                                </form>
                                <form action="{{ route('admin.reject-vendor', $vendor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject and delete this vendor shop?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700 transition">Reject</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4 text-center">No pending vendors.</p>
                    @endforelse
                </div>

                <!-- Pending Agencies -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center justify-between">
                        <span>Pending Agency Approvals</span>
                        <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-1 rounded-full">{{ count($pendingAgencies) }}</span>
                    </h3>

                    @forelse($pendingAgencies as $agency)
                        <div class="flex justify-between items-center py-4 border-b border-gray-100 last:border-0">
                            <div>
                                <!-- Clickable link to view agency details -->
                                <a href="{{ route('admin.agency.show', $agency->id) }}" class="font-bold text-gray-800 hover:text-emerald-600 underline">
                                    {{ $agency->name }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $agency->type }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.approve-agency', $agency->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-700 transition">Approve</button>
                                </form>
                                <form action="{{ route('admin.reject-agency', $agency->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject and delete this agency?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700 transition">Reject</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4 text-center">No pending agencies.</p>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
</body>
</html>