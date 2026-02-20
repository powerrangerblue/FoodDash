<?= $this->extend('layouts/dashboard') ?>

<?php $this->setVar('pageTitle', 'Admin Dashboard — FoodDash'); ?>

<?= $this->section('content') ?>
<div class="row mb-4">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <div>
      <h3 class="m-0">Admin Dashboard</h3>
      <small class="text-muted">System management and platform overview</small>
    </div>
    <button class="btn btn-sm btn-primary" id="refreshBtn">Refresh Data</button>
  </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4" id="summaryRow">
  <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
    <div class="card summary-card text-center border-primary shadow-sm">
      <div class="card-body">
        <small class="text-muted text-uppercase">Total Users</small>
        <h3 class="mt-2 mb-0" id="totalUsers">0</h3>
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
    <div class="card summary-card text-center border-info shadow-sm">
      <div class="card-body">
        <small class="text-muted text-uppercase">Active Drivers</small>
        <h3 class="mt-2 mb-0" id="activeDrivers">0</h3>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
    <div class="card summary-card text-center border-success shadow-sm">
      <div class="card-body">
        <small class="text-muted text-uppercase">Orders Today</small>
        <h3 class="mt-2 mb-0" id="totalOrdersToday">0</h3>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
    <div class="card summary-card text-center border-secondary shadow-sm">
      <div class="card-body">
        <small class="text-muted text-uppercase">Daily Revenue</small>
        <h3 class="mt-2 mb-0" id="dailyRevenue">₱0.00</h3>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
    <div class="card summary-card text-center border-danger shadow-sm">
      <div class="card-body">
        <small class="text-muted text-uppercase">Pending Approvals</small>
        <h3 class="mt-2 mb-0" id="pendingApprovals">0</h3>
      </div>
    </div>
  </div>
</div>

<!-- Recent Orders Table -->
<section id="orders" class="mb-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
          <h5 class="card-title m-0">Recent Orders</h5>
          <small class="text-muted">Monitor and manage orders across all restaurants</small>
        </div>
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
        <table id="ordersTable" class="table table-striped table-hover table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>Order #</th>
              <th>Customer</th>
              <th>Restaurant</th>
              <th>Driver</th>
              <th>Status</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Revenue Summary Table -->
<section id="revenue" class="mb-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="card-title m-0">Revenue Summary (Last 30 Days)</h5>
          <small class="text-muted">By Restaurant</small>
        </div>
      </div>

      <div class="table-responsive">
        <table id="revenueTable" class="table table-striped table-hover table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>Restaurant</th>
              <th>Orders</th>
              <th>Revenue</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  function statusBadge(status) {
    const map = {
      pending: '<span class="badge bg-warning">Pending</span>',
      assigned: '<span class="badge bg-info">Assigned</span>',
      out_for_delivery: '<span class="badge bg-primary">Out for delivery</span>',
      delivered: '<span class="badge bg-success">Delivered</span>',
      cancelled: '<span class="badge bg-danger">Cancelled</span>'
    };
    return map[status] || '<span class="badge bg-secondary">' + status + '</span>';
  }

  function loadDashboard() {
    fetch('<?= site_url('dashboard/admin/data') ?>')
      .then(r => r.json())
      .then(json => {
        // Update metrics
        $('#totalUsers').text(json.metrics.totalUsers);
        $('#totalRestaurants').text(json.metrics.totalRestaurants);
        $('#activeDrivers').text(json.metrics.activeDrivers);
        $('#totalOrdersToday').text(json.metrics.totalOrdersToday);
        $('#dailyRevenue').text('₱' + Number(json.metrics.dailyRevenue).toFixed(2));
        $('#pendingApprovals').text(json.metrics.pendingRestaurants + json.metrics.pendingDrivers);

        // Update orders table
        const ordersBody = document.querySelector('#ordersTable tbody');
        ordersBody.innerHTML = '';
        (json.recentOrders || []).forEach(order => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td><strong>${order.order_number}</strong></td>
            <td>${order.customer_name}</td>
            <td>${order.restaurant_name || '-'}</td>
            <td>${order.driver_name || 'Unassigned'}</td>
            <td>${statusBadge(order.status)}</td>
            <td>₱${parseFloat(order.total_amount).toFixed(2)}</td>
            <td>${new Date(order.created_at).toLocaleDateString()}</td>
            <td><button class="btn btn-sm btn-outline-secondary" onclick="assignDriver(${order.id})">Assign</button></td>
          `;
          ordersBody.appendChild(row);
        });

        // Load and display revenue summary
        fetch('<?= site_url('api/admin/revenue-summary') ?>')
          .then(r => r.json())
          .then(revData => {
            const revBody = document.querySelector('#revenueTable tbody');
            revBody.innerHTML = '';
            (revData.revenueByRestaurant || []).forEach(rest => {
              const row = document.createElement('tr');
              row.innerHTML = `
                <td>${rest.name}</td>
                <td>${rest.orders}</td>
                <td>₱${parseFloat(rest.revenue).toFixed(2)}</td>
              `;
              revBody.appendChild(row);
            });
          });
      })
      .catch(err => console.error(err));
  }

  function assignDriver(orderId) {
    const driverId = prompt('Enter Driver ID:');
    if (!driverId) return;
    
    fetch(`<?= site_url('orders') ?>/${orderId}/assign-driver`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `driver_id=${driverId}`
    })
    .then(r => r.json())
    .then(json => {
      alert(json.message || 'Driver assigned');
      loadDashboard();
    })
    .catch(err => alert('Error: ' + err));
  }

  $('#ordersSearch').on('keyup', function () {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(val) ? '' : 'none';
    });
  });

  $('#statusFilter').on('change', function () {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
      const status = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
      row.style.display = !val || status.includes(val) ? '' : 'none';
    });
  });

  $('#refreshBtn').on('click', loadDashboard);

  $(document).ready(loadDashboard);
</script>
<?= $this->endSection() ?>
