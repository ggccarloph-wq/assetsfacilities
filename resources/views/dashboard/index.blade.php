@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
<div class="page-hero">
    <div>
        <div class="page-hero-eyebrow">NU Clark · Asset Management</div>
        <h1 class="page-hero-title">Welcome back, {{ explode(' ', trim(auth()->user()->name))[0] ?? 'Admin' }}</h1>
        <p class="page-hero-sub">Here's a live overview of your inventory, requisitions, and forecasting. Everything that needs your attention is summarized below.</p>
    </div>
    <div class="page-hero-side">
        <div class="hero-chip">
            <i class="bi bi-box-seam"></i>
            <div>
                <div class="hero-chip-val">{{ $capexCount }}</div>
                <div class="hero-chip-lbl">Assets (CAPEX)</div>
            </div>
        </div>
        <div class="hero-chip">
            <i class="bi bi-check2-circle"></i>
            <div>
                <div class="hero-chip-val">{{ $opexCount }}</div>
                <div class="hero-chip-lbl">Consumables (OPEX)</div>
            </div>
        </div>
        <div class="hero-chip">
            <i class="bi bi-clock-history"></i>
            <div>
                <div class="hero-chip-val">{{ $pending }}</div>
                <div class="hero-chip-lbl">Pending</div>
            </div>
        </div>
        <div class="hero-chip">
            <i class="bi bi-exclamation-triangle"></i>
            <div>
                <div class="hero-chip-val">{{ $lowStock }}</div>
                <div class="hero-chip-lbl">Low Stock</div>
            </div>
        </div>
    </div>
</div>

<div class="surface p-3 mb-3" style="padding:20px!important">
  <div class="module-head mb-3">
    <div>
      <h2 class="module-title">Planning Snapshot</h2>
      <div class="module-note">Operational summary for CAPEX monitoring and OPEX forecasting.</div>
    </div>
  </div>
  <div class="snapshot-grid">
    <div class="snapshot-tile">
      <div class="snapshot-ico si-navy"><i class="bi bi-clipboard-check"></i></div>
      <div>
        <div class="snapshot-num">{{ $approvedRequisitions }}</div>
        <div class="snapshot-lbl">Approved / Finalized Requisitions</div>
      </div>
    </div>
    <div class="snapshot-tile">
      <div class="snapshot-ico si-gold"><i class="bi bi-graph-up-arrow"></i></div>
      <div>
        <div class="snapshot-num">{{ $forecastReadyItems }}</div>
        <div class="snapshot-lbl">Forecast-Ready OPEX Items</div>
      </div>
    </div>
    <div class="snapshot-tile">
      <div class="snapshot-ico si-cyan"><i class="bi bi-box-arrow-up"></i></div>
      <div>
        <div class="snapshot-num">{{ $issuedAssets }}</div>
        <div class="snapshot-lbl">Issued Asset Records</div>
      </div>
    </div>
  </div>

  <hr class="my-3">
  <div class="subhead">
    <h3 class="subhead-title"><i class="bi bi-graph-up"></i> Forecast — Top Restock Priorities</h3>
    <a href="{{ route('forecast.index') }}" class="btn-soft small-btn">Open Forecasting Tool</a>
  </div>
  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>Item</th><th>Predicted Next-Month Demand</th><th>Current Stock</th><th>Suggested Restock</th></tr></thead>
      <tbody>
      @forelse($forecastedItems as $row)
        <tr>
          <td><a href="{{ route('forecast.index', ['item_id' => $row['item']->id]) }}">{{ $row['item']->item_code }} — {{ $row['item']->name }}</a></td>
          <td>{{ $row['forecast']['predicted'] }} {{ $row['item']->unit }}</td>
          <td>{{ $row['forecast']['currentStock'] }} {{ $row['item']->unit }}</td>
          <td><span class="status {{ $row['forecast']['suggestedRestock'] > 0 ? 'low' : 'approved' }}">{{ $row['forecast']['suggestedRestock'] }} {{ $row['item']->unit }}</span></td>
        </tr>
      @empty
        <tr><td colspan="4" class="empty-state">No OPEX items have enough historical usage data yet to forecast (need at least 2 different calendar months logged).</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="panel-grid-2">
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-layers"></i> Inventory Classification (CAPEX vs OPEX)</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="inventoryTypeChart"></canvas></div></div>
    </div>
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-diagram-3"></i> Resource Allocation by Department</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="allocationChart"></canvas></div></div>
    </div>
</div>

<div class="panel-grid-2">
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-activity"></i> Requisition Trends</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="requisitionTrendChart"></canvas></div></div>
    </div>
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-pie-chart"></i> Asset Category Distribution</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="categoryDistributionChart"></canvas></div></div>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Low Stock Items</h2>
            <div class="module-note">Consumables that need replenishment soon</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Code</th><th>Name</th><th>Category</th><th>Stock</th><th>Threshold</th></tr>
            </thead>
            <tbody>
                @forelse($lowStockItems as $item)
                    <tr>
                        <td data-label="Code">{{ $item->item_code }}</td>
                        <td data-label="Name">{{ $item->name }}</td>
                        <td data-label="Category">{{ $item->category->name ?? 'Office Supplies' }}</td>
                        <td data-label="Stock"><span class="status low">{{ $item->quantity }}</span></td>
                        <td data-label="Threshold">{{ $item->low_stock_threshold }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No low stock items at the moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="panel-grid-2">
    <div class="data-panel">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title">Recent Requisitions</h2>
                <div class="module-note">Latest requests submitted in the system</div>
            </div>
            <a href="{{ route('requisitions.index') }}" class="btn-soft small-btn">Open Module</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Reference</th><th>Requester</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentRequisitions as $req)
                    <tr>
                        <td><a href="{{ route('requisitions.show', $req) }}">{{ $req->requisition_no }}</a></td>
                        <td>{{ $req->user->name ?? 'Unknown User' }}</td>
                        <td><span class="status {{ $req->status === 'approved' ? 'approved' : ($req->status === 'rejected' ? 'low' : 'pending') }}">{{ ucfirst($req->status) }}</span></td>
                        <td>{{ optional($req->requested_at)->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No recent requisitions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="data-panel">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title">Recent Activity Proposals</h2>
                <div class="module-note">Latest facility/event proposals routed for signatures</div>
            </div>
            <a href="{{ route('activity-proposals.index') }}" class="btn-soft small-btn">Open Module</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Reference</th><th>Title</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentProposals as $proposal)
                    <tr>
                        <td><a href="{{ route('activity-proposals.show', $proposal) }}">{{ $proposal->proposal_no }}</a></td>
                        <td>{{ $proposal->title }}</td>
                        <td><span class="status {{ $proposal->status === 'approved' ? 'approved' : ($proposal->status === 'rejected' ? 'low' : 'pending') }}">{{ $proposal->statusLabel() }}</span></td>
                        <td>{{ optional($proposal->created_at)->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No recent proposals yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// Chart palette pulled from the same design tokens as the rest of the app
// (navy/gold brand + refined status colors) instead of the old generic
// violet/pink/cyan Chart.js defaults.
const palette = {
    navy: '#1D2657', navyDark: '#0C1330', gold: '#E3B04E', goldLight: '#F0C876',
    cyan: '#3BC3E0', green: '#2BC876', coral: '#E85D6A', lavender: '#8B7FD9', ink: '#000000', line: '#000000'
};
Chart.defaults.font.family = "'Inter', 'Segoe UI', Arial, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = palette.ink;
Chart.defaults.borderColor = palette.line;

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { labels: { boxWidth: 10, usePointStyle: true, pointStyle: 'circle' } } },
    scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: palette.line } }, x: { grid: { display: false } } }
};
new Chart(document.getElementById('inventoryTypeChart'), {
    type: 'bar',
    data: {
        labels: ['Inventory'],
        datasets: [
            { label: 'CAPEX (Assets)', data: [{{ $capexCount }}], backgroundColor: palette.navy, borderRadius: 8, maxBarThickness: 46 },
            { label: 'OPEX (Consumables)', data: [{{ $opexCount }}], backgroundColor: palette.gold, borderRadius: 8, maxBarThickness: 46 }
        ]
    },
    options: chartDefaults
});
new Chart(document.getElementById('allocationChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($allocationByDepartment->pluck('name')) !!},
        datasets: [
            { label: 'CAPEX', data: {!! json_encode($allocationByDepartment->pluck('capex')) !!}, backgroundColor: palette.navy, borderRadius: 6, maxBarThickness: 28 },
            { label: 'OPEX', data: {!! json_encode($allocationByDepartment->pluck('opex')) !!}, backgroundColor: palette.cyan, borderRadius: 6, maxBarThickness: 28 }
        ]
    },
    options: chartDefaults
});
new Chart(document.getElementById('requisitionTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($requisitionTrend->pluck('month_num')->map(fn($m) => date('M', mktime(0,0,0,(int)$m,1)))) !!},
        datasets: [{
            label: 'Requests',
            data: {!! json_encode($requisitionTrend->pluck('total')) !!},
            borderColor: palette.navy,
            backgroundColor: 'rgba(29,38,87,.08)',
            pointBackgroundColor: palette.gold,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: .4,
            fill: true,
            borderWidth: 2.5
        }]
    },
    options: chartDefaults
});
new Chart(document.getElementById('categoryDistributionChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryDistribution->pluck('category_name')) !!},
        datasets: [{
            data: {!! json_encode($categoryDistribution->pluck('total')) !!},
            backgroundColor: [palette.navy, palette.gold, palette.cyan, palette.green, palette.coral, palette.lavender],
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 8
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, pointStyle: 'circle', padding: 14 } } } }
});
</script>
@endpush
