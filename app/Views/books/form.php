<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <a href="<?= site_url('books') ?>" class="text-decoration-none">&larr; Danh sách sách</a>
    <h1 class="h3 mt-2"><?= esc($title) ?></h1>
</div>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endforeach; ?>

<div class="card form-card">
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="title">Tên sách</label>
                    <input class="form-control" id="title" name="title" maxlength="200" value="<?= esc($book['title'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="isbn">ISBN</label>
                    <input class="form-control" id="isbn" name="isbn" maxlength="20" value="<?= esc($book['isbn'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="author">Tác giả</label>
                    <input class="form-control" id="author" name="author" maxlength="150" value="<?= esc($book['author'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="category_name">Thể loại</label>
                    <input class="form-control" id="category_name" name="category_name" list="category-suggestions" maxlength="100" value="<?= esc($categoryName) ?>" placeholder="Nhập hoặc chọn thể loại" required>
                    <datalist id="category-suggestions">
                        <?php foreach ($categories as $category): ?>
                            <?php if ($category['status'] === 'active' || (string) ($book['category_id'] ?? '') === (string) $category['id']): ?>
                                <option value="<?= esc($category['name']) ?>"></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </datalist>
                    <small class="form-text">Có thể nhập tên thể loại mới; hệ thống sẽ tự tạo khi lưu sách.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="publisher">Nhà xuất bản</label>
                    <input class="form-control" id="publisher" name="publisher" maxlength="150" value="<?= esc($book['publisher'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="publication_year">Năm xuất bản</label>
                    <input class="form-control" id="publication_year" name="publication_year" type="number" min="0" max="<?= esc(date('Y')) ?>" value="<?= esc((string) ($book['publication_year'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="price">Giá bán (VND)</label>
                    <input class="form-control" id="price" name="price" type="number" min="0" step="0.01" value="<?= esc((string) ($book['price'] ?? '')) ?>" required>
                </div>
                <?php if (! isset($book['id'])): ?>
                    <div class="col-md-3">
                        <label class="form-label" for="stock">Tồn kho ban đầu</label>
                        <input class="form-control" id="stock" name="stock" type="number" min="0" step="1" value="<?= esc((string) ($book['stock'] ?? 0)) ?>" required>
                    </div>
                <?php else: ?>
                    <div class="col-md-3">
                        <label class="form-label">Tồn kho hiện tại</label>
                        <p class="form-control-plaintext mb-0"><?= esc((string) $book['stock']) ?> cuốn · <a href="<?= site_url('inventory/receive') ?>">Nhập thêm</a></p>
                    </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <label class="form-label" for="status">Trạng thái</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" <?= ($book['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang bán</option>
                        <option value="inactive" <?= ($book['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="4" maxlength="2000"><?= esc($book['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit">Lưu sách</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('books') ?>">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
