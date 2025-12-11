@php use Carbon\Carbon; @endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;700;800&display=swap');

body {
font-family: 'Figtree', sans-serif;
background-color: #FDF2F7;
}

/* Magenta Theme Colors */
:root {
--magenta: #D54F8D;
--magenta-dark: #B63E78;
--magenta-light: #FDE6F1;
--card-radius: 20px;
}

/* Vibrant Card Style */
.metric-card {
background: linear-gradient(135deg, var(--magenta), var(--magenta-dark));
border-radius: var(--card-radius);
padding: 28px;
color: white;
position: relative;
overflow: hidden;
box-shadow: 0 18px 30px rgba(213, 79, 141, 0.25);
transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.metric-card:hover {
transform: translateY(-6px);
box-shadow: 0 25px 45px rgba(213, 79, 141, 0.35);
}

/* Light Panels (Inventory + User Section) */
.panel-card {
background: lavenderblush;
border-radius: var(--card-radius);
padding: 15px;
box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
transition: transform .2s;

}
.panel-card:hover { transform: translateY(-4px);  }

/* Frosted inner section */
.glass-box {
background: rgba(255, 255, 255, 0.65);
backdrop-filter: blur(10px);
border-radius: 14px;
padding: 16px;
}

.stat-number {
font-size: 2.8rem;
font-weight: 800;
margin-top: 5px;
letter-spacing: -1px;
}

.horizontal-scroll {
overflow-x: auto;
padding-bottom: 10px;
}

.scroll-row {
display: flex;
gap: 22px;
min-width: max-content;
}

/* Custom style for low stock item list */
.low-stock-item {
color: #B63E78;
border-left: 4px solid #F87171;
margin-bottom: 8px;
font-weight: 500;
display: grid;
grid-template-columns: 2fr 1fr 0.5fr;
gap: 10px;
align-items: center;
}

@media (max-width: 640px) {
    /* Targeting the h2 element in the header slot */
    h2, .ti {
        font-size: 1.5rem !important; /* Decreased size for mobile */
    }
}
</style>
<x-app-layout> 
    <x-slot name="header">
    {{-- We assume the parent component handles the container width and left alignment (default) --}}
    <div style=" align-items: center; justify-content: center; gap: 0.5rem;">
        
    <h2 style="font-weight: 500; font-size: 2.5rem; font-style: times new roman; color:#B63E78; ">
        Admin Dashboard Overview
    </h2>
    </div>
</x-slot>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div style ="max-width: 1620px; margin: 0 auto; padding: 0 1rem;">
        <div>
            <p>&nbsp;</p>
            <!-- Key Metrics Cards -->
            <div class="horizontal-scroll">
                <div class="scroll-row">
                    <!-- Cake SKUs -->
                    <div class="metric-card min-w-[300px]">
                        <h4 class="text-sm uppercase font-bold opacity-90">Cake SKUs</h4>
                        <p class="stat-number">{{ number_format($availableCakesCount ?? 0) }}</p>
                        <p>Unique products in stock</p>
                    </div>

                    <!-- Add-ons SKUs -->
                    <div class="metric-card min-w-[300px]">
                        <h4 class="text-sm uppercase font-bold opacity-90">Add-ons SKUs</h4>
                        <p class="stat-number">{{ number_format($availableAddOnsCount ?? 0) }}</p>
                        <p>Unique products in stock</p>
                    </div>

                    <!-- Active Cashiers -->
                    <div class="metric-card min-w-[300px]">
                        <h4 class="text-sm uppercase font-bold opacity-90">Active Cashiers</h4>
                        <p class="stat-number">{{ number_format($activeCashiers ?? 0) }}</p>
                        <p>Cashiers currently active</p>
                    </div>

                    <!-- Today's Revenue -->
                    <div class="metric-card min-w-[300px]">
                        <h4 class="text-sm uppercase font-bold opacity-90">Today's Revenue</h4>
                        <p class="stat-number">${{ number_format($salesToday ?? 0, 2) }}</p>
                        <p>Gross revenue today</p>
                    </div>

                    <!-- Low Stock Count -->
                    <div class="metric-card min-w-[300px]">
                        <h4 class="text-sm uppercase font-bold opacity-90">Low Stock Products</h4>
                        <p class="stat-number">{{ number_format($lowStockCount ?? 0) }}</p>
                        <p>Items below minimum stock</p>
                    </div>
                </div>
</div>
        
       <div>
    {{-- ⭐️ START of the two-column container (grid-cols-2) ⭐️ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12"> 
        
        <div class="panel-card">
            <h3 style="font-size: 1.25rem; font: weight 500px;">Cake Stock Inventory</h3>
            <div style="height: 450px;">
                <canvas id="cakeStockChart"></canvas>
                <p id="cake-no-data" class="hidden text-center text-gray-500 mt-10">No cake product data available.</p>
            </div>
        </div>

        {{-- ❌ Removed the unnecessary closing </div> tag and the <p>&nbsp;</p> ❌ --}}
        
        <div class="panel-card">
            <h3 style="font-size: 1.25rem; font: weight 500px;">Add-ons Stock Inventory</h3>
            <div style="height: 450px;">
                <canvas id="addonStockChart"></canvas>
                <p id="addon-no-data" class="hidden text-center text-black-500 mt-10">No add-on product data available.</p>
            </div>
        </div>
        
    {{-- ⭐️ END of the two-column container ⭐️ --}}
    </div>
</div>
        
        <p>&nbsp;</p>
       

      {{-- 3. FILTER FORM and DYNAMIC CHARTS --}}
     {{-- 🛑 NOTE: Assuming this entire block replaces the previous block you shared 🛑 --}}

<div class="panel-card mb-5 ">
    <h1 class="mb-4 ti" style="font-weight: 500; font-size: 2rem; font-style: times new roman; color:#B63E78; ">📊 Sales & Performance Dashboard</h1>
    
    {{-- CORRECTED FORM STRUCTURE --}}
    <form method="GET" action="{{ route('dashboard') }}" 
          class="d-flex align-items-end gap-3 p-3 bg-light rounded shadow-sm">
        
        {{-- 1. Date Range Start (Correctly wrapped in a div) --}}
        
            <label for="start_date" class="form-label fw-bold">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" 
                    value="{{ $selectedStartDate }}" required>
        

        {{-- 2. Date Range End (Correctly wrapped in a div) --}}
        
            <label for="end_date" class="form-label fw-bold">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" 
                    value="{{ $selectedEndDate }}" required>
        

        {{-- 3. Aggregation Interval (Correctly wrapped in a div) --}}
       
            <label for="interval" class="form-label fw-bold">Group Trend By</label>
            <select name="interval" id="interval" class="form-select" required>
                <option value="day" {{ $selectedInterval == 'day' ? 'selected' : '' }}>Day</option>
                <option value="month" {{ $selectedInterval == 'month' ? 'selected' : '' }}>Month</option>
                <option value="year" {{ $selectedInterval == 'year' ? 'selected' : '' }}>Year</option>
            </select>
       
        
        {{-- 4. Submit Button (Colored Magenta) --}}
        <button type="submit" class="btn btn-primary" 
                style="background-color: var(--magenta); border-color: var(--magenta-dark); color: white; height: 38px; width: 120px;">
            Apply Filters
        </button>
    </form>

        {{-- 4. REVENUE TREND & TOP SELLERS (SIDE-BY-SIDE) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10"> 
        {{-- 4. REVENUE TREND (LEFT) & TRANSACTION COUNT (RIGHT) --}}
        <div class="row mt-5">
            
            {{-- COLUMN 1 (LEFT): Revenue Trend Chart (Takes 7/12 width on large screens) --}}
            <div class="col-lg-7 mb-4">
                <div class="panel-card p-4" style="height: 800px;">
                    <h3 style="font-size: 1.35rem; font-weight: 500; margin-bottom:20px">📈 Revenue Trend</h3>
                    <p class="text-muted">Aggregated Per **{{ ucfirst($selectedInterval) }}** ({{ $selectedStartDate }} to {{ $selectedEndDate }})</p>
                    <canvas id="revenueChart" style="margin-top:50px;"></canvas>
                </div>
            </div>

            {{-- COLUMN 2 (RIGHT): Transaction Count Chart (Takes 5/12 width on large screens) --}}
            <div class="col-lg-5 mb-4">
                <div class="panel-card p-4" style="height: 800px;">
                    <h3 style="font-size: 1.35rem; font-weight: 500; margin-bottom:20px;">🛒 Transaction Count Trend</h3>
                    <p class="text-muted">Total transactions per **{{ ucfirst($selectedInterval) }}**.</p>
                    <canvas id="transactionChart" style="margin-top:50px;"></canvas>
                </div>
            </div>
            
        </div>
        
        {{-- 5. TOP PRODUCTS (LEFT) & TOP CASHIER (RIGHT) --}}
        <div class="row mb-5">
            
            {{-- COLUMN 1 (LEFT): Top Products Sold (Takes 7/12 width, aligned with Revenue above) --}}
            <div class="col-lg-7 mb-4">
                <div class="panel-card p-4" style="height: 800px;">
                    <h3 style="font-size: 1.35rem; font-weight: 500; margin-bottom:20px;">⭐ Top 5 Products Sold</h3>
                    <p class="text-muted">Total units sold in the selected period.</p>
                    <canvas id="bestSellerChart" style="margin-top:50px;"></canvas>
                </div>
            </div>

            {{-- MODIFIED BLOCK with fixed height --}}
<div class="col-lg-5 mb-4">
    <div class="panel-card p-4" style="height: 800px;"> 
        <h3 style="font-size: 1.35rem; font-weight: 500;">🧑 Top Cashier Performance</h3>
        <p class="text-muted">Total sales generated in the selected period.</p>
        <canvas id="cashierChart"></canvas>
    </div>
</div>
        </div>
</div>

    {{-- =================================== --}}
    {{-- CHART.JS RENDERING SCRIPT --}}
    {{-- =================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helper function to check for data existence
            const hasData = (data) => data && data.length && data.some(v => v > 0);

            // 1. STOCK CHARTS
            function drawStockCharts() {
                // --- Cake Chart ---
                const cakeLabels = @json($cakeLabels);
                const cakeData = @json($cakeData);

                if (hasData(cakeData)) {
                    new Chart(document.getElementById('cakeStockChart').getContext('2d'), {
                        type: 'bar',
                        data: { labels: cakeLabels, datasets: [{ label: 'Units in Stock', data: cakeData, backgroundColor: '#D54F8D' }] },
                        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true }, y: { ticks: { autoSkip: false } } } }
                    });
                } else {
                     document.getElementById('cake-no-data').classList.remove('hidden');
                     document.getElementById('cakeStockChart').classList.add('hidden');
                }

                // --- Add-ons Chart ---
                const addonLabels = @json($addonLabels);
                const addonData = @json($addonData);

                if (hasData(addonData)) {
                    new Chart(document.getElementById('addonStockChart').getContext('2d'), {
                        type: 'bar',
                        data: { labels: addonLabels, datasets: [{ label: 'Units in Stock', data: addonData, backgroundColor: '#F472B6' }] },
                        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true }, y: { ticks: { autoSkip: false } } } }
                    });
                } else {
                     document.getElementById('addon-no-data').classList.remove('hidden');
                     document.getElementById('addonStockChart').classList.add('hidden');
                }
            }
            drawStockCharts(); 

            // 2. REVENUE TREND CHART (LINE CHART)
            const revenueLabels = @json($revenueLabels);
            const revenueData = @json($revenueData);

            new Chart(document.getElementById('revenueChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: revenueLabels,
                    datasets: [{
                        label: 'Total Revenue',
                        data: revenueData,
                        backgroundColor: 'rgba(213, 79, 141, 0.5)',
                        borderColor: '#D54F8D',
                        tension: 0.2
                    }]
                },
                options: { responsive: true }
            });
            
            // 3. TOP SELLERS CHART (BAR CHART)
            const bestSellerLabels = @json($bestSellerLabels);
            const bestSellerData = @json($bestSellerData);

            new Chart(document.getElementById('bestSellerChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: bestSellerLabels,
                    datasets: [{
                        label: 'Units Sold',
                        data: bestSellerData,
                        backgroundColor: '#6366F1', // Indigo
                    }]
                },
                options: { responsive: true }
            });

            // 4. CASHIER PERFORMANCE CHART (DOUGHNUT CHART)
            const cashierLabels = @json($cashierLabels);
            const cashierData = @json($cashierData);

            new Chart(document.getElementById('cashierChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: cashierLabels,
                    datasets: [{
                        label: 'Total Sales by Cashier',
                        data: cashierData,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    }]
                },
                options: { responsive: true }
            });
        });

        const transactionLabels = @json($revenueLabels); // Re-use the revenue labels
    const transactionData = @json($transactionData); 

    if (transactionData && transactionData.length > 0) { 
        new Chart(document.getElementById('transactionChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: transactionLabels,
                datasets: [{
                    label: 'Total Transactions',
                    data: transactionData,
                    backgroundColor: 'rgba(6, 182, 212, 0.5)', 
                    borderColor: '#06B6D4', 
                    tension: 0.2
                }]
            },
            options: { 
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    </script>

 
</x-app-layout>