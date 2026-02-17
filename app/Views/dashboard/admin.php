<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard — FoodDash</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
  <style>
    body { padding-top: 70px; }
    .sidebar { width: 250px; height: 100vh; position: fixed; top: 56px; left: 0; overflow-y: auto; }
    .content { margin-left: 250px; }
    @media (max-width: 992px) { .sidebar { left: -250px; } .content { margin-left: 0; } }
    .summary-card { min-height: 100px; }
    .nav-link.active { background: rgba(0,0,0,.05); border-radius: .25rem; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#"><strong>FoodDash</strong></a>
    <button class="btn btn-outline-light d-lg-none" id="sidebarToggle">☰</button>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><span class="nav-link text-white-50">Admin Dashboard</span></li>
      </ul>

      <div class="d-flex align-items-center">
        <div class="me-3 text-white-50">Signed in as <strong><?= esc(session('email') ?: 'Admin') ?></strong></div>
        <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-light">Logout</a>
      </div>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<div class="bg-light sidebar border-end d-none d-lg-block" id="sidebar">
  <div class="p-3">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item"><a href="<?= site_url('dashboard/admin') ?>" class="nav-link active">Dashboard</a></li>
      <li class="nav-item"><a href="#orders" class="nav-link">Orders</a></li>
      <li class="nav-item"><a href="#drivers" class="nav-link">Drivers</a></li>
      <li class="nav-item"><a href="#restaurants" class="nav-link">Restaurants</a></li>
      <li class="nav-item"><a href="#reports" class="nav-link">Reports</a></li>
      <li class="nav-item"><a href="#users" class="nav-link">User Management</a></li>
      <li class="nav-item mt-2"><a href="<?= site_url('logout') ?>" class="nav-link text-danger">Logout</a></li>
    </ul>
  </div>
</div>

<!-- Main content -->
<main class="content px-4">
  <div class="container-fluid">
    <!-- Summary cards -->
    <div class="row mb-4" id="summaryRow">
      <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
        <h3 class="m-0">Overview</h3>
        <div>
          <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">Refresh</button>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card summary-card text-center border-primary">
          <div class="card-body">
            <h6 class="card-title">Total Orders Today</h6>
            <h3 id="totalOrders">0</h3>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card summary-card text-center border-success">
          <div class="card-body">
            <h6 class="card-title">Active Deliveries</h6>
            <h3 id="activeDeliveries">0</h3>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card summary-card text-center border-info">
          <div class="card-body">
            <h6 class="card-title">Active Drivers</h6>
            <h3 id="activeDrivers">0</h3>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card summary-card text-center border-warning">
          <div class="card-body">
            <h6 class="card-title">Total Restaurants</h6>
            <h3 id="totalRestaurants">0</h3>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card summary-card text-center border-secondary">
          <div class="card-body">
            <h6 class="card-title">Daily Revenue</h6>
            <h3 id="dailyRevenue">$0.00</h3>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card summary-card text-center border-danger">
          <div class="card-body">
            <h6 class="card-title">Pending Orders</h6>
            <h3 id="pendingOrders">0</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent orders table -->
    <section id="orders" class="mb-5">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title m-0">Recent Orders</h5>
            <div class="d-flex gap-2">
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
            <table id="ordersTable" class="table table-striped table-hover table-sm" style="width:100%">
              <thead>
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
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">Orders per Day (last 7 days)</h5>
              <canvas id="ordersChart" height="140"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">Revenue Trends (last 7 days)</h5>
              <canvas id="revenueChart" height="140"></canvas>
            </div>
          </div>
        </div>

        <div class="col-12 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">Driver Performance</h5>
              <canvas id="driverChart" height="80"></canvas>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
      data: { labels: days, datasets: [{ label: 'Orders', data: orderCounts, backgroundColor: 'rgba(13,110,253,0.1)', borderColor: '#0d6efd', fill: true }] },
      options: { responsive: true }
    });

    const ctx2 = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) revenueChart.destroy();
    revenueChart = new Chart(ctx2, {
      type: 'bar',
      data: { labels: revenueDays, datasets: [{ label: 'Revenue', data: revenueVals, backgroundColor: 'rgba(25,135,84,0.7)' }] },
      options: { responsive: true }
    });

    const ctx3 = document.getElementById('driverChart').getContext('2d');
    if (driverChart) driverChart.destroy();
    driverChart = new Chart(ctx3, {
      type: 'bar',
      data: { labels: drivers, datasets: [{ label: 'Deliveries', data: deliveries, backgroundColor: 'rgba(13,110,253,0.7)' }] },
      options: { responsive: true }
    });
  }

  $(document).ready(function () {
    loadDashboard();
    $('#refreshBtn').on('click', loadDashboard);

    // sidebar toggle for smaller desktop windows
    $('#sidebarToggle').on('click', function () {
      const sb = $('#sidebar');
      if (sb.hasClass('d-none')) sb.removeClass('d-none');
      sb.toggleClass('d-lg-block');
      sb.toggleClass('show');
    });
  });
</script>
</body>
</html>