<!-- Responsive Transaction History Page -->
@php use Carbon\Carbon; @endphp
<style>
    .text-magenta { color: #D54F8D; }
    .bg-soft-pink { background-color: #FFF0F5; }
    .text-header { color: #D54F8D; }
    .bg-magenta { background-color: #D54F8D; }
    .hover\:bg-pink-700:hover { background-color: #C3417E; }
    .border-magenta { border-color: #D54F8D; }
    .font-figtree { font-family: 'Figtree', sans-serif; }

    .input-theme {
        border-color: #FADDE6;
        background-color: #FFF9FC;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .input-theme:focus {
        border-color: #D54F8D;
        box-shadow: 0 0 0 1px #D54F8D;
    }
</style>

<div class="w-full h-full p-2 md:p-4 lg:p-6"> 

    <h1 class="text-2xl md:text-2xl font-extrabold mb-4 md:mb-6 text-header text-center md:text-left">Transaction History</h1>

    <!-- FILTER FORM -->
    <form method="GET" action="{{ route('pos.main') }}" class="mb-6 p-4 border border-pink-200 rounded-xl bg-pink-50 shadow-inner">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

            <input type="hidden" name="view" value="history">

            <!-- DATE FILTER -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Transaction Date</label>
                <input type="date" name="date" value="{{ request('date', Carbon::today()->toDateString()) }}"
                       class="w-full rounded-xl input-theme text-sm">
            </div>

            <!-- TIME FILTER -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Time of Day</label>
                <select name="time" class="w-full rounded-xl input-theme text-sm">
                    <option value="">All Day</option>
                    <option value="morning" {{ request('time')=='morning'?'selected':'' }}>Morning</option>
                    <option value="afternoon" {{ request('time')=='afternoon'?'selected':'' }}>Afternoon</option>
                    <option value="evening" {{ request('time')=='evening'?'selected':'' }}>Evening</option>
                </select>
            </div>

            <!-- BUTTONS -->
            <div  id="filter-form" action="{{ route('pos.transactions.history') }}"class="flex gap-3 col-span-1 sm:col-span-2 lg:col-span-1">
                <button type="submit" class="px-4 bg-magenta text-white font-bold rounded-xl shadow-md hover:bg-pink-700 text-sm">
                    Apply Filter
                </button>

                @if (request('time') || request('date') != Carbon::today()->toDateString())
                <a href="{{ route('pos.main',['view'=>'history','date'=>Carbon::today()->toDateString()]) }}"
                   class="flex-1 px-4  text-sm text-gray-600 hover:text-magenta border border-gray-300 rounded-xl bg-white text-center">
                    Clear
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- TABLE WRAPPER -->
    <div class="overflow-auto border border-pink-200 rounded-xl shadow-xl max-h-[70vh]"> 
        <table class="min-w-full divide-y divide-pink-100 font-figtree text-xs sm:text-sm">
            <thead class="bg-soft-pink sticky top-0 z-10">
                <tr>
                    <th class="py-3 px-4 sm:px-6 text-left font-extrabold text-magenta uppercase">Receipt No.</th>
                    <th class="py-3 px-4 sm:px-6 text-left font-extrabold text-magenta uppercase">Time</th>
                    <th class="py-3 px-4 sm:px-6 text-right font-extrabold text-magenta uppercase">Total</th>
                    <th class="py-3 px-4 sm:px-6 text-center font-extrabold text-magenta uppercase">Method</th>
                    <th class="py-3 px-4 sm:px-6 text-center font-extrabold text-magenta uppercase">Status</th>
                    <th class="py-3 px-4 sm:px-6 text-center font-extrabold text-magenta uppercase">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-pink-50">
                @forelse ($pastTransactions as $order)
                <tr class="hover:bg-pink-50 transition">
                    <td class="py-3 px-4 sm:px-6 font-extrabold text-gray-800">#{{ $order->id }}</td>
                    <td class="py-3 px-4 sm:px-6 text-gray-600">{{ Carbon::parse($order->created_at)->format('h:i A') }}</td>
                    <td class="py-3 px-4 sm:px-6 text-right font-extrabold text-gray-900">₱{{ number_format($order->total_amount,2) }}</td>
                    <td class="py-3 px-4 sm:px-6 text-center text-gray-700">{{ $order->payment_method }}</td>
                    <td class="py-3 px-4 sm:px-6 text-center">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $order->status=='completed'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 sm:px-6 text-center font-bold">
                        <a href="{{ route('pos.transaction.detail',$order) }}" class="text-magenta underline hover:text-pink-700">Review/Refund</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 bg-pink-50 text-sm sm:text-base">
                        <span class="text-magenta font-semibold">No sales history found</span> for the selected period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('filter-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Stop the default form submission (full page refresh)
    
    const form = e.target;
    const formData = new URLSearchParams(new FormData(form)).toString();
    const tableBody = document.getElementById('transaction-table-body');
    const url = form.action + '?' + formData;

    tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Loading transactions...</td></tr>';
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        let html = '';
        if (data.length === 0) {
            html = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 bg-pink-50 text-sm sm:text-base"><span class="text-magenta font-semibold">No sales history found</span> for the selected period.</td></tr>';
        } else {
            data.forEach(order => {
                const statusClass = order.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const totalFormatted = `₱${parseFloat(order.total_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
                
                // Convert timestamp to local time string for display (only if DB stores UTC)
                const time = new Date(order.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

                html += `
                    <tr class="hover:bg-pink-50 transition">
                        <td class="py-3 px-4 sm:px-6 font-extrabold text-gray-800">#${order.id}</td>
                        <td class="py-3 px-4 sm:px-6 text-gray-600">${time}</td>
                        <td class="py-3 px-4 sm:px-6 text-right font-extrabold text-gray-900">${totalFormatted}</td>
                        <td class="py-3 px-4 sm:px-6 text-center text-gray-700">${order.payment_method}</td>
                        <td class="py-3 px-4 sm:px-6 text-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full ${statusClass}">${order.status}</span>
                        </td>
                        <td class="py-3 px-4 sm:px-6 text-center font-bold">
                            <a href="/pos/transaction/${order.id}" class="text-magenta underline hover:text-pink-700">Review/Refund</a>
                        </td>
                    </tr>
                `;
            });
        }
        tableBody.innerHTML = html;
    })
    .catch(error => {
        tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-red-600">Error loading data. Check console.</td></tr>';
        console.error('Fetch Error:', error);
    });
});
</script>