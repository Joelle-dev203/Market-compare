<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agency Dashboard - PriceCheckCameroon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans">

    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-50 border-b">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="#" class="text-2xl font-bold text-emerald-600">PriceCheck<span class="text-amber-500">Cameroon</span></a>
            
            <div class="flex items-center gap-6">
                <span class="text-sm font-bold text-gray-700">
                    Welcome, <span class="text-emerald-600">{{ Auth::guard('agency')->user()->name }}</span>
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-red-500 hover:text-red-700">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Dynamic Form Container --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-1" 
             x-data="{ 
                 transportType: 'flight', 
                 dateRows: [{ departure: '', arrival: '', outbound_stops: '', return_departure: '', return_arrival: '', return_stops: '' }], 
                 flightClasses: [{ class: '', oneway_price: '', roundtrip_price: '', seat: '' }],
                 busClasses: [
                     { class: '', price: '', times: [''] }
                 ]
             }">
            <h2 class="text-lg font-bold mb-4 text-emerald-700">Add New Schedule</h2>
            
            <form action="{{ route('agency.trip.store') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Transport Type Switcher --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Transport Type</label>
                    <select name="type" x-model="transportType" class="w-full p-3 border rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 font-semibold text-emerald-700">
                        <option value="flight">Flight</option>
                        <option value="bus">Bus</option>
                    </select>
                </div>

                {{-- Origin & Destination --}}
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Origin</label>
                        <input type="text" name="origin" required :placeholder="transportType === 'flight' ? 'e.g. Douala (DLA)' : 'e.g. Douala'" class="w-full p-3 border rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Destination</label>
                        <input type="text" name="destination" required :placeholder="transportType === 'flight' ? 'e.g. Paris (CDG)' : 'e.g. Yaoundé'" class="w-full p-3 border rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50">
                    </div>
                </div>

                {{-- ================= FLIGHT SPECIFIC FIELDS ================= --}}
                <div x-show="transportType === 'flight'" class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold text-gray-700">Flight Schedules & Layovers</label>
                            <button type="button" @click="dateRows.push({ departure: '', arrival: '', outbound_stops: '', return_departure: '', return_arrival: '', return_stops: '' })" class="text-xs text-emerald-600 font-bold hover:underline">+ Add Schedule Slot</button>
                        </div>
                        
                        <template x-for="(dRow, dIndex) in dateRows" :key="dIndex">
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl mb-3 space-y-3 relative">
                                
                                {{-- Outbound Leg --}}
                                <div class="border-b pb-2 space-y-2">
                                    <span class="text-[11px] font-extrabold text-emerald-700 uppercase">Outbound Leg</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Departure</label>
                                            <input type="datetime-local" :name="'schedules['+dIndex+'][departure]'" x-model="dRow.departure" :required="transportType === 'flight'" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Arrival</label>
                                            <input type="datetime-local" :name="'schedules['+dIndex+'][arrival]'" x-model="dRow.arrival" :required="transportType === 'flight'" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Outbound Layovers / Stops</label>
                                        <input type="text" :name="'schedules['+dIndex+'][outbound_stops]'" x-model="dRow.outbound_stops" placeholder="e.g. Nairobi (NBO) - 3h layover" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                    </div>
                                </div>

                                {{-- Return Leg (Optional for Round Trip) --}}
                                <div class="space-y-2">
                                    <span class="text-[11px] font-extrabold text-amber-600 uppercase">Return Leg (If Round-Trip)</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Return Departure</label>
                                            <input type="datetime-local" :name="'schedules['+dIndex+'][return_departure]'" x-model="dRow.return_departure" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Return Arrival</label>
                                            <input type="datetime-local" :name="'schedules['+dIndex+'][return_arrival]'" x-model="dRow.return_arrival" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Return Layovers / Stops</label>
                                        <input type="text" :name="'schedules['+dIndex+'][return_stops]'" x-model="dRow.return_stops" placeholder="e.g. Addis Ababa (ADD) - 2h layover" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                    </div>
                                </div>

                                <button type="button" @click="dateRows.splice(dIndex, 1)" x-show="dateRows.length > 1" class="absolute top-2 right-2 text-red-400 font-bold text-sm px-1">&times;</button>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Flight Classes & Pricing</label>
                        <template x-for="(row, index) in flightClasses" :key="index">
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl mb-3 space-y-2 relative">
                                <input type="text" name="class_names[]" placeholder="Class Name (e.g. Business Class)" class="w-full p-2.5 border rounded-lg text-xs bg-white outline-none font-semibold" :required="transportType === 'flight'">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">One-Way (FCFA)</label>
                                        <input type="number" name="oneway_prices[]" placeholder="e.g. 1400000" class="w-full p-2 border rounded-lg text-xs bg-white outline-none" :required="transportType === 'flight'">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Round-Trip (FCFA)</label>
                                        <input type="number" name="roundtrip_prices[]" placeholder="e.g. 2600000" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase">Seat Feature</label>
                                    <input type="text" name="seat_types[]" placeholder="e.g. Lie-flat bed" class="w-full p-2 border rounded-lg text-xs bg-white outline-none">
                                </div>
                                <button type="button" @click="flightClasses.splice(index, 1)" x-show="flightClasses.length > 1" class="absolute top-2 right-2 text-red-400 font-bold text-sm px-1">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="flightClasses.push({ class: '', oneway_price: '', roundtrip_price: '', seat: '' })" class="text-xs text-emerald-600 font-bold hover:underline block mt-1">+ Add flight class</button>
                    </div>
                </div>

                {{-- ================= BUS SPECIFIC FIELDS ================= --}}
                <div x-show="transportType === 'bus'" class="space-y-4" style="display: none;">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bus Classes, Unified Prices & Multiple Times</label>
                        
                        <template x-for="(brow, bidx) in busClasses" :key="bidx">
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl mb-3 space-y-3 relative">
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" :name="'bus_classes['+bidx+'][name]'" x-model="brow.class" placeholder="Class (e.g. Classic)" class="p-2.5 border rounded-lg text-xs outline-none bg-white font-semibold" :required="transportType === 'bus'">
                                    <input type="number" :name="'bus_classes['+bidx+'][price]'" x-model="brow.price" placeholder="Price (FCFA)" class="p-2.5 border rounded-lg text-xs outline-none bg-white" :required="transportType === 'bus'">
                                </div>

                                {{-- Times specific to this class tier --}}
                                <div class="bg-white p-2.5 rounded-lg border border-gray-100 space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase">Departure Times for this Class</label>
                                    
                                    <template x-for="(timeVal, tIdx) in brow.times" :key="tIdx">
                                        <div class="flex gap-2 items-center">
                                            <input type="time" :name="'bus_classes['+bidx+'][times][]'" x-model="brow.times[tIdx]" class="w-full p-2 border rounded-lg text-xs bg-gray-50 outline-none" :required="transportType === 'bus'">
                                            <button type="button" @click="brow.times.splice(tIdx, 1)" x-show="brow.times.length > 1" class="text-red-400 hover:text-red-600 font-bold text-xs px-1">&times;</button>
                                        </div>
                                    </template>
                                    
                                    <button type="button" @click="brow.times.push('')" class="text-[11px] text-emerald-600 font-bold hover:underline block mt-1">+ Add another departure time</button>
                                </div>

                                <button type="button" @click="busClasses.splice(bidx, 1)" x-show="busClasses.length > 1" class="absolute top-2 right-2 text-red-400 hover:text-red-600 font-bold text-sm px-1">&times;</button>
                            </div>
                        </template>

                        <button type="button" @click="busClasses.push({ class: '', price: '', times: [''] })" class="text-xs text-emerald-600 font-bold hover:underline block mt-1">+ Add another class tier (e.g. VIP)</button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition shadow-md mt-4">Publish Schedule</button>
            </form>
        </div>

        {{-- Active Routes Table --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold mb-4 text-emerald-700">Your Active Schedules</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b pb-2">
                        <tr>
                            <th class="pb-3">Type</th>
                            <th class="pb-3">Route</th>
                            <th class="pb-3">Schedules / Timings</th>
                            <th class="pb-3 text-right">Details & Fares</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($trips as $trip)
                        <tr>
                            <td class="py-4 font-bold text-emerald-600 capitalize align-top">{{ $trip->type }}</td>
                            <td class="py-4 font-medium align-top">
                                <div class="text-gray-900 font-bold">{{ $trip->route->departure_city }} &rarr; {{ $trip->route->arrival_city }}</div>
                                <div class="text-xs text-gray-500 mt-1 italic">{{ $trip->stop_details ?? '' }}</div>
                            </td>
                            <td class="py-4 text-xs text-gray-600 align-top">
                                @if(is_array($trip->schedules))
                                    @foreach($trip->schedules as $sched)
                                        <div class="mb-2 bg-gray-50 p-2 rounded border border-gray-100 space-y-1">
                                            @if($trip->type === 'flight')
                                                <div><span class="font-semibold text-emerald-700">Out:</span> {{ \Carbon\Carbon::parse($sched['departure'])->format('M d, Y h:i A') }} &rarr; {{ \Carbon\Carbon::parse($sched['arrival'])->format('M d, Y h:i A') }}</div>
                                                @if(!empty($sched['outbound_stops']))
                                                    <div class="text-[10px] text-gray-500">Stop: {{ $sched['outbound_stops'] }}</div>
                                                @endif

                                                @if(!empty($sched['return_departure']))
                                                    <div class="border-t pt-1 mt-1"><span class="font-semibold text-amber-600">Ret:</span> {{ \Carbon\Carbon::parse($sched['return_departure'])->format('M d, Y h:i A') }} &rarr; {{ \Carbon\Carbon::parse($sched['return_arrival'])->format('M d, Y h:i A') }}</div>
                                                    @if(!empty($sched['return_stops']))
                                                        <div class="text-[10px] text-gray-500">Stop: {{ $sched['return_stops'] }}</div>
                                                    @endif
                                                @endif
                                            @else
                                                <div><span class="font-semibold text-gray-700">{{ $sched['class'] ?? 'Bus' }}:</span> <span class="text-emerald-600 font-bold">{{ $sched['departure'] }}</span></div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </td>
      <td class="py-4 text-right align-top" x-data="{ editMode: false, showDeleteModal: false }">
    <!-- View Fares / Toggle Edit Mode -->
    <div x-show="!editMode">
        @if(is_array($trip->prices))
            <div class="space-y-2">
                @foreach($trip->prices as $className => $details)
                    <div class="p-2.5 bg-gray-50 rounded border border-gray-100 flex justify-between items-center">
                        
                        @if($trip->type === 'flight')
                            <!-- FLIGHT LAYOUT (Restored to look like the detailed second image) -->
                            <div class="text-left">
                                <div class="text-[11px] uppercase font-bold text-emerald-700">{{ $className }}</div>
                                @php
                                    $seatFeature = is_array($details) ? ($details['seat_feature'] ?? $details['feature'] ?? $details['seat'] ?? null) : null;
                                @endphp
                                @if(!empty($seatFeature))
                                    <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">Seat: {{ $seatFeature }}</div>
                                @endif
                            </div>

                            <div class="text-right">
                                @if(is_array($details))
                                    <div class="text-xs text-gray-700"><span class="text-gray-400 text-[10px]">One-Way:</span> FCFA {{ number_format($details['one_way'] ?? 0) }}</div>
                                    @if(!empty($details['round_trip']))
                                        <div class="text-xs text-gray-700"><span class="text-gray-400 text-[10px]">Round-Trip:</span> FCFA {{ number_format($details['round_trip']) }}</div>
                                    @endif
                                @else
                                    <div class="text-xs font-bold text-emerald-600">FCFA {{ number_format($details) }}</div>
                                @endif
                            </div>

                        @else
                            <!-- BUS LAYOUT (Class name + multiple hours & price) -->
                            <div class="text-left">
                                <div class="text-[11px] uppercase font-bold text-emerald-700">{{ $className }}</div>
                                @if(is_array($details) && !empty($details['times']) && is_array($details['times']))
                                    <div class="text-[10px] text-gray-500 font-medium mt-0.5 space-y-0.5">
                                        @foreach($details['times'] as $time)
                                            <div>🕒 {{ $time }}</div>
                                        @endforeach
                                    </div>
                                @elseif(is_array($details) && !empty($details['time']))
                                    <div class="text-[10px] text-gray-500 font-medium mt-0.5">🕒 {{ $details['time'] }}</div>
                                @endif
                            </div>

                            <div class="text-right">
                                @if(is_array($details))
                                    <div class="text-xs font-bold text-gray-800">FCFA {{ number_format($details['price'] ?? $details['one_way'] ?? 0) }}</div>
                                @else
                                    <div class="text-xs font-bold text-emerald-600">FCFA {{ number_format($details) }}</div>
                                @endif
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 mt-3">
            <button @click="editMode = true" class="text-xs font-bold text-amber-600 hover:underline">Edit Prices</button>
            <button @click="showDeleteModal = true" type="button" class="text-xs font-bold text-red-500 hover:underline">Delete Trip</button>
        </div>
    </div>

    <!-- Inline Editor Form -->
    <div x-show="editMode" class="bg-gray-50 p-3 rounded-xl border text-left space-y-3" style="display: none;">
        <div class="flex justify-between items-center border-b pb-2">
            <span class="text-xs font-bold text-amber-700 uppercase">Update Details</span>
            <button @click="editMode = false" class="text-gray-400 font-bold hover:text-gray-600 text-sm">&times;</button>
        </div>

        <form action="{{ route('agency.trip.updatePrices', $trip->id) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            @if(is_array($trip->prices))
                @foreach($trip->prices as $className => $details)
                    <div class="space-y-1 bg-white p-2 rounded border">
                        <input type="text" name="class_names[]" value="{{ $className }}" class="w-full text-xs font-bold p-1 border rounded" required>
                        <div class="grid grid-cols-2 gap-1">
                            <input type="number" name="oneway_prices[]" value="{{ is_array($details) ? ($details['price'] ?? $details['one_way'] ?? 0) : $details }}" placeholder="Price" class="text-xs p-1 border rounded" required>
                            @if($trip->type === 'flight')
                                <input type="number" name="roundtrip_prices[]" value="{{ is_array($details) ? ($details['round_trip'] ?? '') : '' }}" placeholder="Round-trip" class="text-xs p-1 border rounded">
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" @click="editMode = false" class="text-xs text-gray-500 font-bold px-2 py-1">Cancel</button>
                <button type="submit" class="bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded shadow">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div @click.away="showDeleteModal = false" class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 text-left space-y-4">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Delete Trip</h3>
            <p class="text-xs text-gray-600">Are you sure you want to delete this trip permanently?</p>
            
            <div class="flex justify-end gap-2 pt-2">
                <button @click="showDeleteModal = false" type="button" class="text-xs bg-gray-100 text-gray-700 font-bold px-3 py-2 rounded">Cancel</button>
                
                <form action="{{ route('agency.trip.destroy', $trip->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs bg-red-600 text-white font-bold px-3 py-2 rounded shadow">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>