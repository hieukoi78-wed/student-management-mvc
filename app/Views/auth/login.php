<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị · online_shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="login-page">
<main class="login-card">
    <div class="auth-brand">
        <span class="brand-mark" aria-hidden="true">N</span>
        <span>NHÀ SÁCH · HỆ THỐNG QUẢN TRỊ</span>
    </div>
    <h1 class="h3 mb-2">Chào mừng trở lại</h1>
    <p class="text-secondary mb-4">Đăng nhập tài khoản quản trị để tiếp tục công việc.</p>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endforeach; ?>

    <form method="post" action="<?= site_url('login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="username">Tên đăng nhập</label>
            <input class="form-control" id="username" name="username" value="<?= esc($username) ?>" autocomplete="username" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password">Mật khẩu</label>
            <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
    </form>
</main>
</body>
</html>
