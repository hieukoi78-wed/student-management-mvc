<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <a href="<?= site_url('sales') ?>" class="text-decoration-none">&larr; Đơn bán</a>
    <h1 class="h3 mt-2 sales-page-title">Tạo đơn bán tại quầy</h1>
    <p class="text-secondary">Tạo đơn sẽ trừ tồn kho ngay. Mỗi sách chỉ cần một dòng; số lượng nhập phải còn hàng.</p>
</div>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endforeach; ?>

<div class="card form-card">
    <div class="card-body">
        <?php if ($books === []): ?>
            <div class="alert alert-info mb-0">Không có sách đang bán còn hàng. <a href="<?= site_url('inventory/receive') ?>">Nhập kho</a> trước khi tạo đơn.</div>
        <?php else: ?>
            <form method="post" action="<?= site_url('sales') ?>" id="sale-form">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="customer_name">Tên khách hàng (không bắt buộc)</label>
                    <input class="form-control" id="customer_name" name="customer_name" maxlength="120" value="<?= esc($old['customer_name'] ?? '') ?>" placeholder="Khách lẻ">
                </div>
                <div id="sale-items" class="vstack gap-3">
                    <?php
                    $oldBookIds = $old['book_id'] ?? [''];
                    $oldQuantities = $old['quantity'] ?? ['1'];
                    $rowCount = max(1, count($oldBookIds));
                    ?>
                    <?php for ($index = 0; $index < $rowCount; $index++): ?>
                        <div class="row g-2 align-items-end sale-item-row">
                            <div class="col-md-9">
                                <label class="form-label" for="book-<?= $index ?>">Sách</label>
                                <select class="form-select" id="book-<?= $index ?>" name="book_id[]" required>
                                    <option value="">Chọn sách</option>
                                    <?php foreach ($books as $book): ?>
                                        <option value="<?= esc((string) $book['id']) ?>" <?= (string) ($oldBookIds[$index] ?? '') === (string) $book['id'] ? 'selected' : '' ?>>
                                            <?= esc($book['title'] . ' — ' . $book['author'] . ' (còn ' . $book['stock'] . ', ' . number_format((float) $book['price'], 0, ',', '.') . ' ₫)') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="quantity-<?= $index ?>">Số lượng</label>
                                <input class="form-control" id="quantity-<?= $index ?>" name="quantity[]" type="number" min="1" max="1000000" value="<?= esc((string) ($oldQuantities[$index] ?? 1)) ?>" required>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-outline-danger w-100 remove-sale-row" type="button" aria-label="Xóa dòng">X</button>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="mt-3 mb-4">
                    <button class="btn btn-outline-primary" type="button" id="add-sale-row">Thêm dòng sách</button>
                </div>
                <button class="btn btn-primary" type="submit" onclick="return confirm('Xác nhận hoàn tất đơn bán và trừ tồn kho?')">Hoàn tất đơn bán</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('sales') ?>">Hủy</a>
            </form>
            <template id="sale-row-template">
                <div class="row g-2 align-items-end sale-item-row">
                    <div class="col-md-9">
                        <label class="form-label">Sách</label>
                        <select class="form-select" name="book_id[]" required>
                            <option value="">Chọn sách</option>
                            <?php foreach ($books as $book): ?>
                                <option value="<?= esc((string) $book['id']) ?>"><?= esc($book['title'] . ' — ' . $book['author'] . ' (còn ' . $book['stock'] . ', ' . number_format((float) $book['price'], 0, ',', '.') . ' ₫)') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Số lượng</label>
                        <input class="form-control" name="quantity[]" type="number" min="1" max="1000000" value="1" required>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-danger w-100 remove-sale-row" type="button" aria-label="Xóa dòng">X</button>
                    </div>
                </div>
            </template>
            <script>
                const saleItems = document.getElementById('sale-items');
                document.getElementById('add-sale-row').addEventListener('click', () => {
                    saleItems.append(document.getElementById('sale-row-template').content.cloneNode(true));
                });
                saleItems.addEventListener('click', (event) => {
                    if (event.target.closest('.remove-sale-row') && saleItems.querySelectorAll('.sale-item-row').length > 1) {
                        event.target.closest('.sale-item-row').remove();
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
