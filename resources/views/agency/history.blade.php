<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Price History - PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans p-10">
    <main class="max-w-4xl mx-auto">
        <a href="{{ route('agency.dashboard') }}" class="text-emerald-600 font-bold mb-6 block">&larr; Back to Dashboard</a>
        
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-emerald-700 mb-2">Price History</h2>
            <p class="text-gray-500 mb-6 font-semibold">
                {{ $trip->route->departure_city }} → {{ $trip->route->arrival_city }}
            </p>
            
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs uppercase text-gray-400 border-b">
                        <th class="pb-3">Date</th>
                        <th class="pb-3">Class</th>
                        <th class="pb-3 text-right">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse($history as $record)
                    <tr>
                        <td class="py-4 text-gray-600">{{ $record->created_at->format('d M, Y') }}</td>
                        <td class="py-4 font-bold">{{ $record->class_name }}</td>
                        <td class="py-4 text-right font-bold text-emerald-600">{{ number_format($record->price) }} CFA</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-400">No history found for this route.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>