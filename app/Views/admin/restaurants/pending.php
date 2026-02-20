<?= $this->extend('layouts/dashboard') ?>

<?php $this->setVar('pageTitle', 'Approve Restaurants — FoodDash'); ?>

<?= $this->section('content') ?>
<div class="row mb-4">
  <div class="col-12">
    <div>
      <h3 class="m-0">Pending Restaurant Approvals</h3>
      <small class="text-muted">Review and approve new restaurant registrations</small>
    </div>
  </div>
</div>

    <div class="card shadow-sm">
      <div class="card-body">
        <?php if (!empty($restaurants)): ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Restaurant Name</th>
                  <th>Address</th>
                  <th>Applied On</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                  <tr>
                    <td><strong><?= $restaurant['name'] ?></strong></td>
                    <td><?= $restaurant['address'] ?? 'N/A' ?></td>
                    <td><?= date('M d, Y', strtotime($restaurant['created_at'])) ?></td>
                    <td>
                      <button class="btn btn-sm btn-success" onclick="approveRestaurant(<?= $restaurant['id'] ?>)">Approve</button>
                      <button class="btn btn-sm btn-danger" onclick="rejectRestaurant(<?= $restaurant['id'] ?>)">Reject</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <h5>No pending approvals</h5>
            <small>All restaurant registrations have been reviewed</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function approveRestaurant(restaurantId) {
    if (!confirm('Approve this restaurant?')) return;
    
    fetch(`<?= site_url('admin/restaurants') ?>/${restaurantId}/approve`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(r => r.json())
    .then(json => {
      alert(json.message || 'Restaurant approved');
      location.reload();
    })
    .catch(err => alert('Error: ' + err));
  }

  function rejectRestaurant(restaurantId) {
    if (!confirm('Reject this restaurant?')) return;
    
    fetch(`<?= site_url('admin/restaurants') ?>/${restaurantId}/reject`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(r => r.json())
    .then(json => {
      alert(json.message || 'Restaurant rejected');
      location.reload();
    })
    .catch(err => alert('Error: ' + err));
  }
</script>
<?= $this->endSection() ?>
