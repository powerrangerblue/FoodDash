<?= $this->extend('layouts/dashboard') ?>

<?php $this->setVar('pageTitle', 'Edit Menu Item — FoodDash'); ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-6 offset-lg-3">
    <div class="mb-4">
      <h3 class="m-0">Edit Menu Item</h3>
      <small class="text-muted">Update your menu item details</small>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <form id="menuForm">
          <div class="mb-3">
            <label class="form-label">Item Name *</label>
            <input type="text" class="form-control" name="name" value="<?= $item['name'] ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"><?= $item['description'] ?? '' ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Price *</label>
            <input type="number" class="form-control" name="price" step="0.01" value="<?= $item['price'] ?>" required>
          </div>

          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="is_available" value="1" <?= $item['is_available'] ? 'checked' : '' ?>>
            <label class="form-check-label">Available</label>
          </div>

          <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $('#menuForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= site_url('menu/' . $item['id'] . '/update') ?>', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(json => {
      if (json.success) {
        alert(json.message || 'Item updated');
        window.location.href = '<?= site_url('menu') ?>';
      } else {
        alert('Error: ' + (json.error || 'Unknown error'));
      }
    })
    .catch(err => alert('Error: ' + err));
  });
</script>
<?= $this->endSection() ?>
