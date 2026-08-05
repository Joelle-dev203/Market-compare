<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Details | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
    /* Hides the Leaflet and OpenStreetMap attribution text */
    .leaflet-control-attribution {
        display: none !important;
    }
</style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-800 mb-6 inline-block font-semibold">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Vendor Shop Verification Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 text-gray-700">
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Shop / Business Name</span>
                <p class="text-lg font-bold text-gray-900">{{ $vendor->shop_name ?? $vendor->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Owner / Account Name</span>
                <p class="font-medium">{{ $vendor->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Email Address</span>
                <p class="font-medium">{{ $vendor->email ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Phone Number</span>
                <p class="font-medium text-gray-900 font-bold">
                    {{ (!empty($vendor->phone_number) && $vendor->phone_number !== null) ? $vendor->phone_number : 'Not provided' }}
                </p>
            </div>
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Market Location</span>
                <p class="text-emerald-600 font-bold"><i class="fas fa-map-marker-alt mr-1"></i> {{ $vendor->location ?? $vendor->market_name ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Live Map with Cameroon/Douala Search (No API Key Required) -->
        <div class="mb-8">
            <!-- <div class="flex justify-between items-center mb-2">
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Live Map & Location Search</span>
                <span class="text-xs text-gray-500">OpenStreetMap (Free & No API Key)</span>
            </div> -->
            
            <!-- Search bar to find locations in Douala/Cameroon instantly -->
            <div class="mb-3 flex gap-2">
                <input type="text" id="mapSearchInput" placeholder="Search place in Douala (e.g., Akwa, Bonaberi, Deido)..." class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button type="button" id="searchBtn" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-emerald-700 transition">Search</button>
            </div>

            <div id="vendorMap" class="w-full h-80 rounded-xl border border-gray-200 z-10"></div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 border-t pt-6">
            <form action="{{ route('admin.approve-vendor', $vendor->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition">Approve Vendor</button>
            </form>
            
            <form action="{{ route('admin.reject-vendor', $vendor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject and delete this vendor shop?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-red-700 transition">Reject & Delete</button>
            </form>
        </div>
    </div>

    <!-- Map Script -->
    <script>
        // Defaults to Douala center coordinates
        var lat = {{ $vendor->latitude ?? $vendor->lat ?? 4.0511 }};
        var lng = {{ $vendor->longitude ?? $vendor->lng ?? 9.7679 }};
        var shopName = "{{ $vendor->shop_name ?? $vendor->name ?? 'Vendor Shop' }}";
        var shopLocation = "{{ $vendor->location ?? 'Douala, Cameroon' }}";

        var map = L.map('vendorMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>" + shopName + "</b><br>" + shopLocation)
            .openPopup();

        // Real-time search focusing on Cameroon regions using OpenStreetMap Nominatim
        function searchLocation() {
            var query = document.getElementById('mapSearchInput').value;
            if(!query) return;

            fetch('https://nominatim.openstreetmap.org/search?format=json&countrycodes=cm&q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if(data && data.length > 0) {
                        var foundLat = parseFloat(data[0].lat);
                        var foundLon = parseFloat(data[0].lon);
                        
                        map.setView([foundLat, foundLon], 16);
                        marker.setLatLng([foundLat, foundLon]);
                        marker.bindPopup("<b>" + shopName + "</b><br>" + data[0].display_name).openPopup();
                    } else {
                        alert('Location not found in Cameroon. Try typing a known area like Akwa, Bonanjo, or Bonaberi.');
                    }
                })
                .catch(err => console.error('Search error:', err));
        }

        document.getElementById('searchBtn').addEventListener('click', searchLocation);
        document.getElementById('mapSearchInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                searchLocation();
            }
        });
    </script>
</body>
</html>