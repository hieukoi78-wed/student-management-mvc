<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <a href="<?= site_url('inventory') ?>" class="text-decoration-none">&larr; Lịch sử nhập kho</a>
    <h1 class="h3 mt-2">Nhập thêm sách vào kho</h1>
    <p class="text-secondary">Chọn sách đã có để cộng số lượng. Nếu chưa có sách, thêm đầu sách mới ngay tại đây.</p>
</div>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endforeach; ?>

<div class="card form-card">
    <div class="card-body">
        <?php if ($books === []): ?>
            <div class="alert alert-info">
                Chưa có đầu sách trong hệ thống. Hãy thêm sách trước khi ghi nhận nhập kho.
            </div>
            <a class="btn btn-primary" href="<?= site_url('books/create') ?>">＋ Thêm sách mới</a>
        <?php else: ?>
            <form method="post" action="<?= site_url('inventory/receive') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="book_id">Đầu sách</label>
                    <select class="form-select" id="book_id" name="book_id" required>
                        <option value="">Chọn sách</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= esc((string) $book['id']) ?>" <?= $selectedBookId === (string) $book['id'] ? 'selected' : '' ?>>
                                <?= esc($book['title'] . ' — ' . $book['author'] . ' (ISBN ' . $book['isbn'] . ', tồn ' . $book['stock'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="quantity">Số lượng nhập thêm</label>
                    <input class="form-control" id="quantity" name="quantity" type="number" min="1" max="1000000" step="1" value="<?= esc((string) ($old['quantity'] ?? '')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="notes">Ghi chú / số phiếu nhập</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="500"><?= esc($old['notes'] ?? '') ?></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Ghi nhận nhập kho</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('inventory') ?>">Hủy</a>
            </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
