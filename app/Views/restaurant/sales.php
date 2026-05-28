<?= $this->extend('layouts/dashboard') ?>

<?php $this->setVar('pageTitle', 'Sales - FoodDash'); ?>

<?php
    $summaryCards = $summaryCards ?? [];
    $selectedSummary = $selectedSummary ?? ['revenue' => 0, 'orders' => 0];
    $transactions = $transactions ?? [];
    $queryBase = $queryBase ?? ['range' => 'this_month'];
    $selectedRange = (string) ($selectedRange ?? 'this_month');
    $selectedStartDate = (string) ($selectedStartDate ?? date('Y-m-01'));
    $selectedEndDate = (string) ($selectedEndDate ?? date('Y-m-d'));
    $selectedRangeLabel = (string) ($selectedRangeLabel ?? 'This Month');
    $restaurantName = (string) ($restaurantName ?? 'Restaurant');
    $generatedAt = (string) ($generatedAt ?? date('M d, Y h:i A'));
    $trendLabels = $trendLabels ?? [];
    $trendValues = $trendValues ?? [];
    $comparisonLabels = $comparisonLabels ?? [];
    $comparisonValues = $comparisonValues ?? [];

    $exportCsvUrl = site_url('sales?' . http_build_query(array_merge($queryBase, ['export' => 'csv'])));
    $exportPdfUrl = site_url('sales?' . http_build_query(array_merge($queryBase, ['export' => 'pdf'])));
    $isCustom = $selectedRange === 'custom';
?>

<?= $this->section('head') ?>
<style>
    .sales-hero {
        overflow: hidden;
        position: relative;
    }

    .sales-hero::after {
        content: '';
        position: absolute;
        inset: auto -60px -80px auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
    }

    .sales-section-card {
        border: 1px solid rgba(58, 63, 69, 0.16);
        border-radius: .85rem;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }

    html[data-theme="dark"] .sales-section-card {
        background: rgba(26, 26, 26, 0.92);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .sales-stat-card {
        height: 100%;
    }

    .sales-stat-card .fd-stat-card {
        background: rgba(255, 255, 255, 0.94);
        border-radius: .85rem;
    }

    html[data-theme="dark"] .sales-stat-card .fd-stat-card {
        background: rgba(26, 26, 26, 0.92);
    }

    .sales-filter-card .form-label,
    .sales-filter-card h5,
    .sales-filter-card small,
    .sales-filter-card strong,
    .sales-filter-card .btn,
    .sales-filter-card .form-select,
    .sales-filter-card .form-control {
        position: relative;
        z-index: 1;
    }

    html[data-theme="dark"] .sales-filter-card {
        background: rgba(26, 26, 26, 0.92);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .sales-table-card .table-light {
        background-color: rgba(242, 194, 0, 0.08);
    }

    html[data-theme="dark"] .sales-table-card .table {
        color: #f8fafc;
    }

    html[data-theme="dark"] .sales-table-card .table-light th {
        background: rgba(242, 194, 0, 0.12);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.08);
    }

    @media print {
        .navbar-dashboard,
        .fd-sidebar,
        .sales-controls,
        .no-print {
            display: none !important;
        }

        body {
            padding-top: 0 !important;
            background: #fff !important;
        }

        .fd-shell,
        .fd-content {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .card,
        .sales-filter-panel,
        .sales-card {
            box-shadow: none !important;
            border-color: #d1d5db !important;
        }

        .page-break {
            break-after: page;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="fd-page-header mb-4 sales-hero">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 position-relative">
        <div>
            <small class="text-uppercase opacity-75">Sales Analytics</small>
            <h3 class="m-0">Sales Dashboard</h3>
            <p class="mb-0 mt-2 opacity-75">Monitor earnings, completed orders, and report activity for <?= esc($restaurantName) ?>.</p>
        </div>
        <div class="text-end">
            <div class="fw-semibold">Date Generated</div>
            <div><?= esc($generatedAt) ?></div>
            <div class="small opacity-75 mt-1">Report range: <?= esc($selectedRangeLabel) ?></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php
        $cardMeta = [
            'daily' => ['title' => 'Daily Sales', 'accent' => 'success'],
            'weekly' => ['title' => 'Weekly Sales', 'accent' => 'primary'],
            'monthly' => ['title' => 'Monthly Sales', 'accent' => 'warning'],
            'lifetime' => ['title' => 'Total Lifetime Sales', 'accent' => 'dark'],
        ];
    ?>
    <?php foreach ($cardMeta as $key => $meta): ?>
        <?php $card = $summaryCards[$key] ?? ['revenue' => 0, 'orders' => 0]; ?>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="sales-stat-card h-100">
                <div class="fd-stat-card">
                    <small class="fd-stat-label"><?= esc($meta['title']) ?></small>
                    <p class="fd-stat-value text-<?= esc($meta['accent']) ?>">₱<?= number_format((float) ($card['revenue'] ?? 0), 2) ?></p>
                    <div class="text-muted"><?= (int) ($card['orders'] ?? 0) ?> orders</div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card shadow-sm border-0 sales-filter-card mb-4 sales-controls">
    <div class="card-body p-3 p-lg-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
        <div>
            <h5 class="mb-1">Filter Sales Report</h5>
            <small class="text-muted">Choose a preset range or generate a custom report.</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('sales?' . http_build_query(['range' => 'today'])) ?>">Today</a>
            <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('sales?' . http_build_query(['range' => 'this_week'])) ?>">This Week</a>
            <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('sales?' . http_build_query(['range' => 'this_month'])) ?>">This Month</a>
            <button type="button" class="btn btn-outline-dark btn-sm" id="printSalesBtn">Print Report</button>
            <a class="btn btn-outline-success btn-sm" href="<?= esc($exportCsvUrl) ?>">Export CSV</a>
            <a class="btn btn-outline-danger btn-sm" href="<?= esc($exportPdfUrl) ?>">Export PDF</a>
        </div>
    </div>

    <form method="get" action="<?= site_url('sales') ?>" class="row g-3 align-items-end" id="salesFilterForm">
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Date Range</label>
            <select name="range" id="salesRange" class="form-select">
                <option value="today" <?= $selectedRange === 'today' ? 'selected' : '' ?>>Today</option>
                <option value="this_week" <?= $selectedRange === 'this_week' ? 'selected' : '' ?>>This Week</option>
                <option value="this_month" <?= $selectedRange === 'this_month' ? 'selected' : '' ?>>This Month</option>
                <option value="custom" <?= $selectedRange === 'custom' ? 'selected' : '' ?>>Custom Date Range</option>
            </select>
        </div>
        <div class="col-md-3 custom-range-field <?= $isCustom ? '' : 'd-none' ?>">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" id="salesStartDate" class="form-control" value="<?= esc($selectedStartDate) ?>">
        </div>
        <div class="col-md-3 custom-range-field <?= $isCustom ? '' : 'd-none' ?>">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" id="salesEndDate" class="form-control" value="<?= esc($selectedEndDate) ?>">
        </div>
        <div class="col-md-2 col-lg-2">
            <button type="submit" class="btn btn-primary w-100">Generate Report</button>
        </div>
    </form>

    <div class="mt-3 p-3 rounded-3 bg-light border">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <small class="text-muted text-uppercase d-block">Selected Range Summary</small>
                <strong><?= esc($selectedRangeLabel) ?></strong>
            </div>
            <div class="d-flex gap-4 flex-wrap">
                <div>
                    <small class="text-muted d-block">Total Sales</small>
                    <strong class="text-success">₱<?= number_format((float) ($selectedSummary['revenue'] ?? 0), 2) ?></strong>
                </div>
                <div>
                    <small class="text-muted d-block">Total Orders</small>
                    <strong><?= (int) ($selectedSummary['orders'] ?? 0) ?></strong>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="row g-4 mb-4 page-break">
    <div class="col-12 col-xl-7">
        <div class="card shadow-sm border-0 sales-section-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="card-title m-0">Sales Trend</h5>
                        <small class="text-muted">Daily revenue for the selected range.</small>
                    </div>
                </div>
                <div style="height: 320px;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-5">
        <div class="card shadow-sm border-0 sales-section-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="card-title m-0">Sales Comparison</h5>
                        <small class="text-muted">Top sales days in the selected range.</small>
                    </div>
                </div>
                <div style="height: 320px;">
                    <canvas id="salesComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 sales-section-card sales-table-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="card-title m-0">Sales Transactions</h5>
                <small class="text-muted">Delivered orders only.</small>
            </div>
            <div class="text-muted small">Completed orders included in report: <?= (int) ($selectedSummary['orders'] ?? 0) ?></div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Payment Method</th>
                        <th>Total Amount</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($transactions)): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><strong><?= esc($transaction['order_number'] ?? $transaction['id']) ?></strong></td>
                                <td><?= esc($transaction['customer_name'] ?? '-') ?></td>
                                <td><?= esc(date('M d, Y', strtotime((string) ($transaction['created_at'] ?? 'now')))) ?></td>
                                <td><?= esc(date('h:i A', strtotime((string) ($transaction['created_at'] ?? 'now')))) ?></td>
                                <td><?= esc($transaction['payment_method'] ?: '-') ?></td>
                                <td><strong>₱<?= number_format((float) ($transaction['total_amount'] ?? 0), 2) ?></strong></td>
                                <td><span class="badge bg-success">Delivered</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No delivered sales found for the selected range.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const isDarkTheme = document.documentElement.dataset.theme === 'dark';
    const chartGridColor = isDarkTheme ? 'rgba(255,255,255,0.12)' : 'rgba(15,23,42,0.08)';
    const chartTickColor = isDarkTheme ? '#E5E7EB' : '#475569';

    const salesTrendLabels = <?= json_encode($trendLabels, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const salesTrendValues = <?= json_encode($trendValues, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const salesComparisonLabels = <?= json_encode($comparisonLabels, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const salesComparisonValues = <?= json_encode($comparisonValues, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

    let salesTrendChart = null;
    let salesComparisonChart = null;

    function renderSalesTrendChart() {
        const ctx = document.getElementById('salesTrendChart');
        if (!ctx) {
            return;
        }

        if (salesTrendChart) {
            salesTrendChart.destroy();
        }

        salesTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesTrendLabels,
                datasets: [{
                    label: 'Sales (₱)',
                    data: salesTrendValues,
                    borderColor: '#1C4B4A',
                    backgroundColor: 'rgba(28, 75, 74, 0.18)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartGridColor },
                        ticks: {
                            color: chartTickColor,
                            callback: (value) => '₱' + Number(value).toFixed(0),
                        },
                    },
                    x: {
                        grid: { color: chartGridColor },
                        ticks: { color: chartTickColor },
                    },
                },
            },
        });
    }

    function renderSalesComparisonChart() {
        const ctx = document.getElementById('salesComparisonChart');
        if (!ctx) {
            return;
        }

        if (salesComparisonChart) {
            salesComparisonChart.destroy();
        }

        salesComparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: salesComparisonLabels,
                datasets: [{
                    label: 'Top Sales Days',
                    data: salesComparisonValues,
                    borderRadius: 8,
                    backgroundColor: ['#F2C200', '#1C4B4A', '#F3D39A', '#6B7C87', '#C49300', '#123737', '#D6A21A'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartGridColor },
                        ticks: {
                            color: chartTickColor,
                            callback: (value) => '₱' + Number(value).toFixed(0),
                        },
                    },
                    x: {
                        grid: { color: chartGridColor },
                        ticks: { color: chartTickColor },
                    },
                },
            },
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderSalesTrendChart();
        renderSalesComparisonChart();

        const rangeSelect = document.getElementById('salesRange');
        const customFields = document.querySelectorAll('.custom-range-field');
        const printBtn = document.getElementById('printSalesBtn');

        function toggleCustomFields() {
            const isCustom = rangeSelect && rangeSelect.value === 'custom';
            customFields.forEach(field => field.classList.toggle('d-none', !isCustom));
        }

        if (rangeSelect) {
            rangeSelect.addEventListener('change', toggleCustomFields);
            toggleCustomFields();
        }

        if (printBtn) {
            printBtn.addEventListener('click', function () {
                window.print();
            });
        }
    });
</script>
<?= $this->endSection() ?>