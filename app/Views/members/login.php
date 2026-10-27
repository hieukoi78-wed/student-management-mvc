<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập thành viên · Nhà sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="login-page">
<main class="login-card">
    <div class="auth-brand">
        <span class="brand-mark" aria-hidden="true">N</span>
        <span>NHÀ SÁCH · CỘNG ĐỒNG ĐỌC SÁCH</span>
    </div>
    <h1 class="h3 mb-1">Nhà sách</h1>
    <p class="text-secondary mb-4">Đăng nhập để tham gia chat với thành viên và quản trị viên.</p>
    <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endforeach; ?>
    <form method="post" action="<?= site_url('member/login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="username">Tên đăng nhập hoặc email</label>
            <input class="form-control" id="username" name="username" value="<?= esc($username) ?>" autocomplete="username" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password">Mật khẩu</label>
            <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
    </form>
    <p class="text-center mt-3 mb-0">Chưa có tài khoản? <a href="<?= site_url('member/register') ?>">Đăng ký thành viên</a></p>
</main>
</body>
</html>
