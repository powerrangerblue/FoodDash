<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard — FoodDash</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Admin Dashboard</h1>
    <a class="btn btn-outline-secondary" href="<?php echo site_url('logout'); ?>">Logout</a>
  </div>

  <div class="card">
    <div class="card-body">
      <p>Welcome, <strong>Admin</strong>. You are authenticated and authorized to access administrative features.</p>
      <ul>
        <li>Manage restaurants</li>
        <li>View reports</li>
        <li>System settings</li>
      </ul>
    </div>
  </div>
</div>
</body>
</html>