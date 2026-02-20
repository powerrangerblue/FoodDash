<?= $this->extend('layouts/dashboard') ?>

<?php $this->setVar('pageTitle', 'Manage Orders — FoodDash'); ?>

<?= $this->section('content') ?>
<div class="row mb-4">
  <div class="col-12">
    <div>
      <h3 class="m-0">Order Management</h3>
      <small class="text-muted">View and manage all your orders</small>
    </div>
  </div>
</div>

    <div class="card shadow-sm">
      <div class="card-body">
        <?php if (!empty($orders)): ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Order #</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th>Amount</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td><strong><?= $order['order_number'] ?></strong></td>
                    <td><?= $order['customer_name'] ?></td>
                    <td>
                      <?php
                        $statusClass = match ($order['status']) {
                          'pending' => 'warning',
                          'confirmed' => 'info',
                          'preparing' => 'primary',
                          'ready_for_pickup' => 'success',
                          'completed' => 'success',
                          'cancelled' => 'danger',
                          default => 'secondary'
                        };
                      ?>
                      <span class="badge bg-<?= $statusClass ?>"><?= ucwords(str_replace('_', ' ', $order['status'])) ?></span>
                    </td>
                    <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary" onclick="updateStatus(<?= $order['id'] ?>)">Update Status</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <h5>No orders yet</h5>
            <small>Your orders will appear here</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function updateStatus(orderId) {
    const newStatus = prompt('Enter new status (pending, confirmed, preparing, ready_for_pickup, completed, cancelled):');
    if (!newStatus) return;
    
    fetch(`<?= site_url('orders') ?>/${orderId}/status`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `status=${newStatus}`
    })
    .then(r => r.json())
    .then(json => {
      if (json.success) {
        alert('Order status updated');
      } else {
        alert('Error: ' + (json.error || 'Unknown error'));
      }
      location.reload();
    })
    .catch(err => alert('Error: ' + err));
  }
</script>
<?= $this->endSection() ?>
