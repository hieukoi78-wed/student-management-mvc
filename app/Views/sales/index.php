<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1 sales-page-title">Đơn bán tại quầy</h1>
        <p class="text-secondary mb-0">Lịch sử giao dịch đã hoàn tất; đơn bán sẽ cập nhật tồn kho và báo cáo.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url('sales/create') ?>">Tạo đơn bán</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Mã đơn</th>
                    <th scope="col">Thời gian</th>
                    <th scope="col">Khách hàng</th>
                    <th scope="col">Thu ngân</th>
                    <th scope="col" class="text-end">Tổng tiền</th>
                    <th scope="col">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($orders === []): ?>
                <tr><td class="text-center text-secondary py-4" colspan="6">Chưa có đơn bán nào.</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($order['order_number']) ?></td>
                        <td><?= esc($order['sold_at']) ?></td>
                        <td><?= esc($order['customer_name'] ?: 'Khách lẻ') ?></td>
                        <td><?= esc($order['admin_name']) ?></td>
                        <td class="text-end"><?= esc(number_format((float) $order['total_amount'], 0, ',', '.')) ?> ₫</td>
                        <td><span class="badge <?= $order['status'] === 'completed' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $order['status'] === 'completed' ? 'Hoàn tất' : 'Đã hủy' ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
