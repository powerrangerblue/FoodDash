<?= $this->extend('layouts/dashboard') ?>

<?php $this->setVar('pageTitle', 'Admin Dashboard — FoodDash'); ?>

<?= $this->section('content') ?>
  <!-- Summary cards -->
  <div class="row mb-4" id="summaryRow">
    <div class="col-12 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div>
        <h3 class="m-0">Admin Overview</h3>
        <small class="text-muted">Key metrics for today and recent activity</small>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-primary" id="refreshBtn">Refresh data</button>
      </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
      <div class="card summary-card text-center border-primary shadow-sm">
        <div class="card-body">
          <small class="text-muted text-uppercase">Total Orders Today</small>
          <h3 class="mt-2 mb-0" id="totalOrders">0</h3>
        </div>
      </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
      <div class="card summary-card text-center border-success shadow-sm">
        <div class="card-body">
          <small class="text-muted text-uppercase">Active Deliveries</small>
          <h3 class="mt-2 mb-0" id="activeDeliveries">0</h3>
        </div>
      </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
      <div class="card summary-card text-center border-info shadow-sm">
        <div class="card-body">
          <small class="text-muted text-uppercase">Active Drivers</small>
          <h3 class="mt-2 mb-0" id="activeDrivers">0</h3>
        </div>
      </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
      <div class="card summary-card text-center border-warning shadow-sm">
        <div class="card-body">
          <small class="text-muted text-uppercase">Total Restaurants</small>
          <h3 class="mt-2 mb-0" id="totalRestaurants">0</h3>
        </div>
      </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
      <div class="card summary-card text-center border-secondary shadow-sm">
        <div class="card-body">
          <small class="text-muted text-uppercase">Daily Revenue</small>
          <h3 class="mt-2 mb-0" id="dailyRevenue">$0.00</h3>
        </div>
      </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
      <div class="card summary-card text-center border-danger shadow-sm">
        <div class="card-body">
          <small class="text-muted text-uppercase">Pending Orders</small>
          <h3 class="mt-2 mb-0" id="pendingOrders">0</h3>
        </div>
      </div>
    </div>

  <!-- Recent orders table -->
  <section id="orders" class="mb-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
          <div>
            <h5 class="card-title m-0">Recent Orders</h5>
            <small class="text-muted">Monitor and manage the latest orders</small>
          </div>
          <div class="d-flex gap-2 ms-auto">
            <select id="statusFilter" class="form-select form-select-sm">
              <option value="">All statuses</option>
              <option value="pending">Pending</option>
              <option value="assigned">Assigned</option>
              <option value="out_for_delivery">Out for delivery</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <input id="ordersSearch" type="search" class="form-control form-control-sm" placeholder="Search orders">
          </div>
        </div>

        <div class="table-responsive">
          <table id="ordersTable" class="table table-striped table-hover table-sm align-middle" style="width:100%">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Restaurant</th>
                <th>Assigned Driver</th>
                <th>Delivery Status</th>
                <th>Total Amount</th>
                <th>Placed</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- Reports and analytics -->
  <section id="reports" class="mb-5">
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Orders per Day (last 7 days)</h5>
            <canvas id="ordersChart" height="140"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Revenue Trends (last 7 days)</h5>
            <canvas id="revenueChart" height="140"></canvas>
          </div>
        </div>
      </div>

      <div class="col-12 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Driver Performance</h5>
            <canvas id="driverChart" height="80"></canvas>
          </div>
        </div>
      </div>
    </div>
  </section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ordersTable = $('#ordersTable').DataTable({
    columns: [
      { data: 'order_number' },
      { data: 'customer_name' },
      { data: 'restaurant_name' },
      { data: 'driver_name' },
      { data: 'status', orderable: false },
      { data: 'total_amount' },
      { data: 'created_at' },
      { data: null, orderable: false },
    ],
    pageLength: 10,
    lengthChange: false,
    order: [[6, 'desc']],
    language: { search: "" },
  });

  function statusBadge(status) {
    const map = {
      pending: ['warning','Pending'],
      assigned: ['info','Assigned'],
      out_for_delivery: ['primary','Out for delivery'],
      delivered: ['success','Delivered'],
      cancelled: ['danger','Cancelled']
    };
    const s = map[status] || ['secondary', status];
    return `<span class="badge bg-${s[0]}">${s[1]}</span>`;
  }

  function actionColumn(row) {
    const statuses = ['pending','assigned','out_for_delivery','delivered','cancelled'];
    let opts = statuses.map(s => `<option value="${s}" ${s===row.status? 'selected':''}>${s.replace(/_/g,' ')}</option>`).join('');
    return `
      <div class="d-flex gap-2 align-items-center">
        <select class="form-select form-select-sm status-select" data-id="${row.id}">${opts}</select>
        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${row.id}">Delete</button>
      </div>
    `;
  }

  // Populate table and UI
  function loadDashboard() {
    fetch('<?= site_url('dashboard/admin/data') ?>')
      .then(r => r.json())
      .then(json => {
        // metrics
        $('#totalOrders').text(json.metrics.totalOrdersToday);
        $('#activeDeliveries').text(json.metrics.activeDeliveries);
        $('#activeDrivers').text(json.metrics.activeDrivers);
        $('#totalRestaurants').text(json.metrics.totalRestaurants);
        $('#dailyRevenue').text('$' + Number(json.metrics.dailyRevenue).toFixed(2));
        $('#pendingOrders').text(json.metrics.pendingOrders);

        // table
        ordersTable.clear();
        const rows = json.recentOrders.map(o => ({
          ...o,
          total_amount: '$' + parseFloat(o.total_amount).toFixed(2),
          status: statusBadge(o.status),
          action: actionColumn(o),
        }));
        ordersTable.rows.add(rows).draw();

        // redraw action column and attach handlers
        $('#ordersTable tbody').off('change', '.status-select');
        $('#ordersTable tbody').on('change', '.status-select', function () {
          const id = $(this).data('id');
          const newStatus = $(this).val();
          if (!confirm('Change status to "' + newStatus.replace(/_/g,' ') + '"?')) { loadDashboard(); return; }
          $.post('<?= site_url('dashboard/order') ?>/' + id + '/status', { status: newStatus })
            .done(() => { loadDashboard(); })
            .fail(() => { alert('Failed to update status'); loadDashboard(); });
        });

        // search/filter
        $('#ordersSearch').on('keyup', function () { ordersTable.search(this.value).draw(); });
        $('#statusFilter').on('change', function () { ordersTable.column(4).search(this.value ? this.value : '').draw(); });

        // charts
        renderCharts(json.ordersPerDay, json.revenueTrends, json.driverPerformance);
      })
      .catch(err => console.error(err));
  }

  let ordersChart, revenueChart, driverChart;
  function renderCharts(ordersPerDay, revenueTrends, driverPerformance) {
    const css = getComputedStyle(document.documentElement);
    const fdPrimary = (css.getPropertyValue('--fd-primary') || '#F2C200').trim();
    const fdEspresso = (css.getPropertyValue('--fd-espresso') || '#241C0C').trim();
    const fdAccent = (css.getPropertyValue('--fd-accent') || '#6B7C87').trim();
    const gridColor = 'rgba(36, 28, 12, 0.12)';
    const tickColor = 'rgba(36, 28, 12, 0.70)';

    const days = ordersPerDay.map(r => r.day);
    const orderCounts = ordersPerDay.map(r => parseInt(r.count,10));
    const revenueDays = revenueTrends.map(r => r.day);
    const revenueVals = revenueTrends.map(r => parseFloat(r.total));
    const drivers = driverPerformance.map(d => d.driver);
    const deliveries = driverPerformance.map(d => parseInt(d.deliveries,10));

    const ctx1 = document.getElementById('ordersChart').getContext('2d');
    if (ordersChart) ordersChart.destroy();
    ordersChart = new Chart(ctx1, {
      type: 'line',
      data: {
        labels: days,
        datasets: [{
          label: 'Orders',
          data: orderCounts,
          backgroundColor: fdPrimary + '33',
          borderColor: fdPrimary,
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            labels: { color: tickColor }
          }
        },
        scales: {
          x: {
            grid: { color: gridColor },
            ticks: { color: tickColor }
          },
          y: {
            grid: { color: gridColor },
            ticks: { color: tickColor }
          }
        }
      }
    });

    const ctx2 = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) revenueChart.destroy();
    revenueChart = new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: revenueDays,
        datasets: [{
          label: 'Revenue',
          data: revenueVals,
          backgroundColor: fdAccent
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            labels: { color: tickColor }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: tickColor }
          },
          y: {
            grid: { color: gridColor },
            ticks: { color: tickColor }
          }
        }
      }
    });

    const ctx3 = document.getElementById('driverChart').getContext('2d');
    if (driverChart) driverChart.destroy();
    driverChart = new Chart(ctx3, {
      type: 'bar',
      data: {
        labels: drivers,
        datasets: [{
          label: 'Deliveries',
          data: deliveries,
          backgroundColor: fdPrimary
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            labels: { color: tickColor }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: tickColor }
          },
          y: {
            grid: { color: gridColor },
            ticks: { color: tickColor }
          }
        }
      }
    });
  }

  $(document).ready(function () {
    loadDashboard();
    $('#refreshBtn').on('click', loadDashboard);
  });
</script>
<?= $this->endSection() ?>