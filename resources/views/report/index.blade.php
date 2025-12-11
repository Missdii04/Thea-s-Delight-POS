<!-- resources/views/admin/reports/index.blade.php (Final Attempt) -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Summary Sales Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-3xl font-bold mb-6">📈 Business Performance Overview</h1>
                
                <!-- ... (KPIs remain the same) ... -->
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Lifetime KPIs</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-indigo-50 p-6 rounded-lg shadow-md border-l-4 border-indigo-600">
                        <p class="text-sm font-medium text-indigo-600">Total Lifetime Revenue</p>
                        <p class="text-4xl font-extrabold text-gray-900 mt-1">
                            ${{ number_format($metrics['totalRevenue'], 2) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg shadow-md border-l-4 border-gray-600">
                        <p class="text-sm font-medium text-gray-600">Total Completed Orders</p>
                        <p class="text-4xl font-extrabold text-gray-900 mt-1">
                            {{ number_format($metrics['totalOrders']) }}
                        </p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-lg shadow-md border-l-4 border-yellow-600">
                        <p class="text-sm font-medium text-yellow-600">Average Order Value</p>
                        <p class="text-4xl font-extrabold text-gray-900 mt-1">
                            ${{ number_format($metrics['averageOrderValue'], 2) }}
                        </p>
                    </div>
                </div>
                
                <!-- Sales Trend Chart Area -->
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">FINAL TEST CHART INTEGRATION</h2>

                <div class="bg-white p-8 rounded-lg shadow-xl border">
                    <canvas id="salesChart" style="max-height: 400px;"></canvas>
                    @if (empty($chartJsData['labels']))
                         <p class="text-gray-500 text-center pt-8">No completed sales recorded in the last 7 days to display the chart.</p>
                    @endif
                </div>

                <!-- Link to detailed inventory report -->
                <div class="mt-8">
                    <a href="{{ route('admin.reports.inventory') }}" class="text-pink-600 hover:underline font-semibold">
                        &rarr; View Detailed Inventory Stock Levels
                    </a>
                </div>

            </div>
        </div>
    </div>
    
    <!-- ⭐️ CRITICAL FIX: Load CDN and execute script IMMEDIATELY after ⭐️ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ⭐️ FIX: Use window.onload for guaranteed library execution ⭐️
        window.onload = function () {
            const chartData = @json($chartJsData); 
            
            // Only attempt to render if the Chart object exists, and we have data points
            if (typeof Chart !== 'undefined' && chartData.datasets[0].data.length > 0) {
                const ctx = document.getElementById('salesChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets.map(dataset => ({
                            ...dataset,
                            tension: 0.3,
                            fill: true
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Revenue ($)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true
                            }
                        }
                    }
                });
            } else {
                 // If data is zero, ensure the canvas is visible but the message is shown
                 const canvas = document.getElementById('salesChart');
                 if (canvas) {
                     canvas.style.display = 'none'; // Hide canvas if no data
                 }
            }
        };
    </script>
</x-app-layout>