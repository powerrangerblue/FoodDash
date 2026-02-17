<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodDash - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">FoodDash Login</h4>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?php $err = session()->getFlashdata('error');
                            if (is_array($err)) {
                                foreach ($err as $e) { echo esc($e) . '<br>'; }
                            } else { echo esc($err); }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?php echo esc(session()->getFlashdata('success')); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo site_url('login'); ?>" method="post">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="<?php echo site_url('forgot'); ?>">Forgot Password?</a>
                            <button class="btn btn-primary">Login</button>
                        </div>
                    </form>

                </div>
            </div>

            <p class="text-muted text-center mt-3">Use <strong>admin@example.com</strong> / <em>AdminPass123</em> or <strong>restaurant@example.com</strong> / <em>RestaurantPass123</em></p>
        </div>
    </div>
</div>
</body>
</html>
