<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin' ?> — SurveySystem</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        .navbar { background: #333; color: #fff; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #fff; text-decoration: none; margin-left: 15px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; }
        .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; }
        th { background: #f8f8f8; font-weight: bold; }
        tr:hover { background: #f9f9f9; }
        .btn { padding: 7px 14px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; display: inline-block; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-danger  { background: #dc3545; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-info    { background: #17a2b8; color: #fff; }
        .btn:hover { opacity: .85; }
        input[type="text"], input[type="password"], input[type="file"], select {
            width: 100%; padding: 8px 10px; border: 1px solid #ccc;
            border-radius: 4px; font-size: 14px; margin-top: 4px;
        }
        label { font-weight: bold; font-size: 14px; }
        .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="navbar">
    <strong>SurveySystem Admin</strong>
    <div>
        Welcome, <?= esc(session()->get('admin_username')) ?> |
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
        <a href="<?= base_url('admin/logout') ?>">Logout</a>
    </div>
</div>

<div class="container">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>

</div>

<?= $this->renderSection('scripts') ?>
</body>
</html>