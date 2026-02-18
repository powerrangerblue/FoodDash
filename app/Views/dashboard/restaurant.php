<?= $this->extend('layouts/dashboard') ?>

<?php $this->setVar('pageTitle', 'Restaurant Dashboard — FoodDash'); ?>

<?= $this->section('content') ?>
<div class="row mb-4">
  <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
      <h3 class="m-0">Restaurant Dashboard</h3>
      <small class="text-muted">Manage your menu, orders, and performance</small>
    </div>
    <div class="d-flex gap-2">
      <a href="#" class="btn btn-sm btn-primary">View all orders</a>
      <a href="#" class="btn btn-sm btn-outline-secondary">Manage menu</a>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-4 mb-3">
    <div class="card shadow-sm h-100">
      <div class="card-body text-center">
        <small class="text-muted text-uppercase">Today’s Orders</small>
        <h3 class="mt-2 mb-0" id="restTodayOrders">0</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card shadow-sm h-100">
      <div class="card-body text-center">
        <small class="text-muted text-uppercase">Pending Orders</small>
        <h3 class="mt-2 mb-0" id="restPendingOrders">0</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card shadow-sm h-100">
      <div class="card-body text-center">
        <small class="text-muted text-uppercase">Menu Items</small>
        <h3 class="mt-2 mb-0" id="restMenuItems">0</h3>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-7 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="card-title m-0">Active Orders</h5>
            <small class="text-muted">Orders currently being prepared or delivered</small>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0" id="restOrdersTable">
            <thead class="table-light">
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="card-title m-0">Menu Snapshot</h5>
            <small class="text-muted">Quick view of your top items</small>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0" id="restMenuTable">
            <thead class="table-light">
              <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Available</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // Placeholder: here you can hook AJAX calls to load real restaurant dashboard data
  $(function () {
    // Example static data to keep the UI looking complete
    $('#restTodayOrders').text('12');
    $('#restPendingOrders').text('3');
    $('#restMenuItems').text('24');

    const orders = [
      { id: 'FD-101', customer: 'John Doe', status: 'Preparing', total: '$18.50' },
      { id: 'FD-102', customer: 'Jane Smith', status: 'Ready', total: '$25.00' },
      { id: 'FD-103', customer: 'Mark Lee', status: 'Out for delivery', total: '$32.75' },
    ];

    const $ordersBody = $('#restOrdersTable tbody');
    orders.forEach(o => {
      $ordersBody.append(`
        <tr>
          <td>${o.id}</td>
          <td>${o.customer}</td>
          <td><span class="badge bg-primary-subtle text-primary">${o.status}</span></td>
          <td>${o.total}</td>
        </tr>
      `);
    });

    const menu = [
      { name: 'Cheese Burger', price: '$8.99', available: true },
      { name: 'Margherita Pizza', price: '$12.50', available: true },
      { name: 'Chicken Wrap', price: '$7.25', available: false },
    ];

    const $menuBody = $('#restMenuTable tbody');
    menu.forEach(m => {
      $menuBody.append(`
        <tr>
          <td>${m.name}</td>
          <td>${m.price}</td>
          <td>
            <span class="badge ${m.available ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'}">
              ${m.available ? 'Yes' : 'No'}
            </span>
          </td>
        </tr>
      `);
    });
  });
</script>
<?= $this->endSection() ?>