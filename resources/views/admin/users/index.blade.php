<x-app-layout>
    <style>
        /* Define the magenta theme colors for consistency */
        .text-magenta { color: #D54F8D; }
        .bg-magenta { background-color: #D54F8D; }
        .hover\:bg-pink-700\:hover:hover { background-color: #C3417E; }
        .bg-soft-pink { background-color: #FFF0F5; } 
    </style>

    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-magenta leading-tight">
            {{ __('User & Cashier Management') }}
        </h2>
    </x-slot>



    <div class="py-12 bg-soft-pink">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold mb-6 text-magenta">👥 User & Cashier Management</h1>
            
            {{-- Success/Error Messages (Themed) --}}
            @if (session('success'))
                <div class="p-4 mb-4 text-sm font-semibold text-white bg-magenta rounded-lg shadow-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm font-semibold text-red-800 bg-red-100 rounded-lg shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 3. Static User Activity Boxes (Moved to the bottom) --}}
        <div class="mt-8 pt-4 border-t border-gray-100">
            <h4 class="text-xl font-bold text-magenta mb-3">System Metrics</h4>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-3 border rounded-lg bg-pink-50">
                    <p class="text-xs font-bold text-magenta">Total Users</p>
                    <p class="text-3xl font-extrabold">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="p-3 border rounded-lg bg-pink-50">
                    <p class="text-xs font-bold text-magenta">Admin Accounts</p>
                    <p class="text-3xl font-extrabold">{{ number_format($adminCount) }}</p>
                </div>
                <div class="p-3 border rounded-lg bg-pink-50">
                    <p class="text-xs font-bold text-magenta">Active Cashiers</p>
                    <p class="text-3xl font-extrabold">{{ number_format($activeCashiers) }}</p>
                </div>
            </div>
        </div>
        
    
            <div style="margin-top:50px;" class="bg-white shadow-2xl rounded-xl overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-soft-pink text-magenta uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Email</th>
                            <th class="py-3 px-6 text-center">Role</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($users as $user)
                        <tr class="border-b border-pink-100 hover:bg-pink-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $user->name }}</td>
                            <td class="py-3 px-6 text-left">{{ $user->email }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="py-1 px-3 rounded-full text-xs font-semibold 
                                    @if($user->role === 'admin') bg-magenta text-white 
                                    @else bg-pink-100 text-magenta 
                                    @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="py-1 px-3 rounded-full text-xs font-semibold 
                                    @if($user->is_active) bg-green-100 text-green-700 
                                    @else bg-red-100 text-red-700 
                                    @endif">
                                    {{ $user->is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-3">
                                
                                {{-- EDIT BUTTON --}}
                                <a href="{{ route('admin.users.edit', $user) }}" title="Edit" class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                                    Edit
                                </a>

                                @php
                                    // Assuming $isLastAdmin is calculated in the controller
                                    $isLastAdmin = ($user->role === 'admin' && $adminCount <= 1);
                                @endphp

                                @if ($isLastAdmin)
                                    <span class="text-gray-400 text-sm">Cannot Modify Last Admin</span>

                                @elseif ($user->is_active)
                                    {{-- STATUS: ACTIVE (SHOW SUSPEND BUTTON) --}}
                                    <button 
                                        onclick="openUserActionModal('{{ route('admin.users.deactivate', $user) }}', 'Suspend', '{{ $user->name }}')"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        Suspend
                                    </button>

                                @else
                                    {{-- STATUS: SUSPENDED (SHOW ACTIVATE BUTTON) --}}
                                    <span class="text-red-600 text-sm">Suspended</span>
                                    
                                    <button 
                                    onclick="openUserActionModal('{{ route('admin.users.activate', $user) }}', 'Activate', '{{ $user->name }}')"
                                    class="text-green-600 hover:text-green-800 text-sm">
                                    Activate
                                </button>

                                @endif

                                {{-- DELETE BUTTON (Removed as per previous request) --}}
                            </div>
                        </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">No users found in the system.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>

            
            
        

    <div class="panel-card max-w-7xl mt-10">
    <div class="bg-white shadow-2xl rounded-xl p-6">
        <h3 class="text-2xl font-extrabold text-magenta mb-4">💰 Cashier Sales Performance Report</h3>

        {{-- 1. Date Range & Cashier Filter Form --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex items-end space-x-4">
            {{-- Cashier Selection --}}
            <div>
                <label for="cashier_id" class="block text-sm font-medium text-gray-700">Select Cashier:</label>
                <select name="cashier_id" id="cashier_id"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:ring-magenta focus:border-magenta sm:text-sm">
                    <option value=""> All Cashiers (Aggregated)  &nbsp&nbsp&nbsp </option>
                    @foreach ($cashiersForFilter as $cashier)
                        <option value="{{ $cashier->id }}" {{ $selectedCashierId == $cashier->id ? 'selected' : '' }}>
                            {{ $cashier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Start Date Input --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                <input type="date" name="start_date" id="start_date" 
                    value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-magenta focus:border-magenta sm:text-sm">
            </div>

            {{-- End Date Input --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
                <input type="date" name="end_date" id="end_date" 
                    value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-magenta focus:border-magenta sm:text-sm">
            </div>

            {{-- Group By Interval (Only relevant for granular reports) --}}
            <div>
                <label for="report_interval" class="block text-sm font-medium text-gray-700">Group By:</label>
                <select name="report_interval" id="report_interval"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-magenta focus:border-magenta sm:text-sm">
                    <option value="day" {{ $reportInterval == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="week" {{ $reportInterval == 'week' ? 'selected' : '' }}>Week</option>
                    <option value="month" {{ $reportInterval == 'month' ? 'selected' : '' }}>Month</option>
                </select>
            </div>
            
            {{-- Apply Button --}}
            <button type="submit" class="px-4 py-2 bg-magenta text-white rounded-md text-sm hover:bg-pink-700 transition duration-150">
                Apply Filters
            </button>
        </form>
        
        <p class="mb-4 text-sm text-gray-600">
            Report Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}.
        </p>

        {{-- 2. CONDITIONAL REPORT DISPLAY --}}
        @if ($selectedCashierId)
            {{-- DETAILED TRANSACTION LIST (Specific Cashier Selected) --}}
            <h4 class="text-xl font-bold text-magenta mb-3 mt-6">Detailed Transactions</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-pink-100">
                    <thead class="bg-soft-pink text-magenta uppercase text-xs leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Order ID</th>
                            <th class="py-3 px-6 text-left">Date/Time</th>
                            <th class="py-3 px-6 text-right">Total Amount (PHP)</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($detailedOrders as $order)
                        <tr class="border-b border-pink-50 hover:bg-pink-50">
                            <td class="py-3 px-6 text-left">{{ $order->id }}</td>
                            <td class="py-3 px-6 text-left">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-6 text-right font-bold">{{ number_format($order->correct_total_amount) }}</td>
                            <td class="py-3 px-6 text-center">{{ ucfirst($order->status) }}</td>
                            <td class="py-3 px-6 text-center">
                                <a href="{{ 
                                    route('admin.orders.show', $order) 
                                    }}?cashier_id={{ $selectedCashierId }}&start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}&report_interval={{ $reportInterval }}" 
                                class="...">
                                    Review/Refund
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">No individual transactions found for this cashier in the selected period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            {{-- AGGREGATED CASHIER REPORT (All Cashiers) --}}
            <h4 class="text-xl font-bold text-magenta mb-3 mt-6">Aggregated Cashier Totals</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-pink-100">
                    <thead class="bg-soft-pink text-magenta uppercase text-xs leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Cashier Name</th>
                            <th class="py-3 px-6 text-right">Total Sales (PHP)</th>
                            <th class="py-3 px-6 text-right">Total Transactions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($cashierReport as $report)
                        <tr class="border-b border-pink-50 hover:bg-pink-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap font-medium">{{ $report['name'] }}</td>
                            <td class="py-3 px-6 text-right font-bold">{{ number_format($report['total_sales']) }}</td>
                            <td class="py-3 px-6 text-right">{{ number_format($report['total_transactions']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500">No sales transactions found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
        
        {{-- 3. Static User Activity Boxes (Bottom Section) --}}
        </div>
</div>
        
        
        
        
    </div>

    </div>
</div>
<!-- Reusable Action Confirmation Modal -->
<div id="userActionModal"
     class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-3" id="modalActionTitle">Confirm Action</h2>
        <p class="text-gray-700 mb-6" id="modalActionMessage"></p>

        <div class="flex justify-end space-x-3">
            <button onclick="closeUserActionModal()"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Cancel
            </button>

            <form id="userActionForm" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" id="modalConfirmBtn"
                        class="px-4 py-2 text-white rounded">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openUserActionModal(actionUrl, actionName, userName) {
        const modal = document.getElementById('userActionModal');

        // Update modal text
        document.getElementById('modalActionTitle').innerText = actionName + " User";
        document.getElementById('modalActionMessage').innerText =
            `Are you sure you want to ${actionName.toLowerCase()} "${userName}"?`;

        // Update button color
        const confirmBtn = document.getElementById('modalConfirmBtn');
        if (actionName === "Suspend") {
            confirmBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            confirmBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        } else {
            confirmBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
            confirmBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        }

        // Set form action
        document.getElementById('userActionForm').action = actionUrl;

        // Show modal
        modal.classList.remove('hidden');
    }

    function closeUserActionModal() {
        document.getElementById('userActionModal').classList.add('hidden');
    }
</script>

</x-app-layout>