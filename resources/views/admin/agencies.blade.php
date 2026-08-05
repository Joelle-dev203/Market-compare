<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Agencies | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Alpine.js CDN for modals/drawers -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100" x-data="{ activeAgencyModal: null }">
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
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Registered Agencies</h2>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Location</th>
                            <th class="p-4">Type</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agencies as $agency)
                        <tr class="border-b hover:bg-gray-50">
                            <!-- Clickable Name triggers Alpine.js Drawer -->
                            <td class="p-4">
                                <button type="button" @click="activeAgencyModal = 'agency-{{ $agency->id }}'" class="text-emerald-600 font-bold hover:underline text-left">
                                    {{ $agency->name }}
                                </button>
                            </td>
                            <td class="p-4 text-gray-700">{{ $agency->location }}</td>
                            <td class="p-4 capitalize text-gray-700 font-medium">{{ $agency->type }}</td>
                            
                            <!-- Status -->
                            <td class="p-4">
                                <span class="{{ $agency->is_approved ? 'text-emerald-600' : 'text-amber-600' }} font-bold">
                                    {{ $agency->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="p-4 flex items-center gap-2 flex-wrap">
                                <!-- Verify / Verified Toggle Button -->
                                <form action="{{ route('admin.approve-agency', $agency->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs {{ isset($agency->verified_at) && $agency->verified_at ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white px-3 py-1 rounded transition">
                                        {{ isset($agency->verified_at) && $agency->verified_at ? 'Verified' : 'Verify' }}
                                    </button>
                                </form>

                                <!-- Deactivate / Activate Button -->
                                <form action="{{ route('admin.toggle-agency-status', $agency->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs {{ $agency->is_approved ? 'bg-amber-500 hover:bg-amber-600' : 'bg-blue-500 hover:bg-blue-600' }} text-white px-3 py-1 rounded transition">
                                        {{ $agency->is_approved ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <!-- Delete Button with Confirmation -->
                                <form action="{{ route('admin.reject-agency', $agency->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this agency?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL / DRAWER FOR AGENCY TRIPS SELECTION -->
    @foreach($agencies as $agency)
    <div x-show="activeAgencyModal === 'agency-{{ $agency->id }}'" 
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         x-transition.opacity>
        
        <div @click.away="activeAgencyModal = null" 
             class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden border border-gray-100 max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-3">
                    @if($agency->logo_path)
                        <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="" class="w-12 h-12 object-contain rounded-xl border bg-white p-1">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <i class="fas fa-building"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-900">{{ $agency->name }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Select a specific trip below to view full details</p>
                    </div>
                </div>
                <button @click="activeAgencyModal = null" class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300 transition font-bold">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body (List of Trips) -->
            <div class="p-6 overflow-y-auto space-y-4 flex-1">
                @forelse($agency->trips ?? [] as $modalTrip)
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
                            <div class="text-xs font-bold text-gray-700 text-center mb-1.5">{{ $agency->name }}</div>
                            <a href="{{ route('trips.show', $modalTrip->id) }}" 
                               class="block w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-center transition shadow-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 text-sm font-medium">
                        No trips currently registered for this agency.
                    </div>
                @endforelse
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
</body>
</html>