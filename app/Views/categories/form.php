<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <a href="<?= site_url('categories') ?>" class="text-decoration-none">&larr; Thể loại sách</a>
    <h1 class="h3 mt-2"><?= esc($title) ?></h1>
</div>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endforeach; ?>

<div class="card form-card">
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="name">Tên thể loại</label>
                <input class="form-control" id="name" name="name" maxlength="100" value="<?= esc($category['name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="description">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000"><?= esc($category['description'] ?? '') ?></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label" for="status">Trạng thái</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" <?= ($category['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang dùng</option>
                    <option value="inactive" <?= ($category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng dùng</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Lưu</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('categories') ?>">Hủy</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
