<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký thành viên · Nhà sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="login-page">
<main class="login-card">
    <div class="auth-brand">
        <span class="brand-mark" aria-hidden="true">N</span>
        <span>NHÀ SÁCH · CỘNG ĐỒNG ĐỌC SÁCH</span>
    </div>
    <h1 class="h3 mb-1">Tạo tài khoản thành viên</h1>
    <p class="text-secondary mb-4">Tham gia phòng chat chung hoặc nhắn tin riêng với quản trị viên.</p>
    <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endforeach; ?>
    <form method="post" action="<?= site_url('member/register') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="full_name">Họ tên</label>
            <input class="form-control" id="full_name" name="full_name" maxlength="120" value="<?= esc($old['full_name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="username">Tên đăng nhập</label>
            <input class="form-control" id="username" name="username" maxlength="80" value="<?= esc($old['username'] ?? '') ?>" autocomplete="username" required>
            <small class="form-text text-secondary">Có thể dùng chữ có dấu, số, dấu cách và các ký tự . @ _ -.</small>
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" id="email" name="email" type="email" maxlength="190" value="<?= esc($old['email'] ?? '') ?>" autocomplete="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Mật khẩu (ít nhất 10 ký tự)</label>
            <input class="form-control" id="password" name="password" type="password" minlength="10" autocomplete="new-password" required>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password_confirm">Nhập lại mật khẩu</label>
            <input class="form-control" id="password_confirm" name="password_confirm" type="password" minlength="10" autocomplete="new-password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Đăng ký</button>
    </form>
    <p class="text-center mt-3 mb-0">Đã có tài khoản? <a href="<?= site_url('member/login') ?>">Đăng nhập</a></p>
</main>
</body>
</html>
