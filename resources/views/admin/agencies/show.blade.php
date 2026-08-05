<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Details | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet CSS for Map -->
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

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Travel Agency Verification Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 text-gray-700">
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Agency Name</span>
                <p class="text-lg font-bold text-gray-900">{{ $agency->name }}</p>
            </div>
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Agency Type</span>
                <p class="font-medium capitalize"><span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $agency->type ?? 'N/A' }}</span></p>
            </div>
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Phone Number</span>
                <p class="font-medium">{{ $agency->phone_number ?? 'Not provided' }}</p>
            </div>
            <div>
                <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider">Main Station Location</span>
                <p class="text-emerald-600 font-bold"><i class="fas fa-map-marker-alt mr-1"></i> {{ $agency->location ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Interactive Map Preview -->
        <div class="mb-8">
            <span class="font-semibold text-gray-400 block text-xs uppercase tracking-wider mb-2">Terminal / Station Map Pin</span>
            <div id="agencyMap" class="w-full h-64 rounded-xl border border-gray-200 z-10"></div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 border-t pt-6">
            <form action="{{ route('admin.approve-agency', $agency->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition">Approve Agency</button>
            </form>
            
            <form action="{{ route('admin.reject-agency', $agency->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject and delete this travel agency?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-red-700 transition">Reject & Delete</button>
            </form>
        </div>
    </div>

    <!-- Map Script -->
    <script>
        var lat = {{ $agency->latitude ?? 4.0511 }}; 
        var lng = {{ $agency->longitude ?? 9.7679 }};

        var map = L.map('agencyMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>{{ $agency->name }}</b><br>Travel Agency Terminal")
            .openPopup();
    </script>
</body>
</html>