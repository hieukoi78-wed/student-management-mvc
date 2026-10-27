<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">Lịch sử nhập kho</h1>
        <p class="text-secondary mb-0">Theo dõi số lượng sách nhập thêm và biến động tồn kho.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url('inventory/receive') ?>">Nhập thêm sách</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Thời gian</th>
                    <th scope="col">Sách</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Loại giao dịch</th>
                    <th scope="col" class="text-end">Số lượng</th>
                    <th scope="col">Quản trị viên</th>
                    <th scope="col">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($movements === []): ?>
                <tr><td class="text-center text-secondary py-4" colspan="7">Chưa có lịch sử nhập kho.</td></tr>
            <?php else: ?>
                <?php foreach ($movements as $movement): ?>
                    <?php
                    $isReceived = $movement['movement_type'] === 'received';
                    $isSold = $movement['movement_type'] === 'sold';
                    $typeLabel = $isReceived ? 'Nhập thêm' : ($isSold ? 'Bán sách' : 'Tồn đầu kỳ');
                    $quantityLabel = $isSold ? '-' . $movement['quantity'] : '+' . $movement['quantity'];
                    ?>
                    <tr>
                        <td><?= esc($movement['created_at']) ?></td>
                        <td class="fw-semibold"><?= esc($movement['title']) ?></td>
                        <td><?= esc($movement['isbn']) ?></td>
                        <td><span class="badge <?= $isReceived ? 'text-bg-success' : ($isSold ? 'text-bg-primary' : 'text-bg-secondary') ?>"><?= esc($typeLabel) ?></span></td>
                        <td class="text-end"><?= esc($quantityLabel) ?></td>
                        <td><?= esc($movement['admin_name'] ?? 'Hệ thống') ?></td>
                        <td><?= esc($movement['notes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
