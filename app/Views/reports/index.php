<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1 class="h3 mb-1">Báo cáo bán hàng</h1>
    <p class="text-secondary mb-0">Doanh thu chỉ tính các đơn tại quầy đã hoàn tất; thời gian theo múi giờ Việt Nam.</p>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['label' => 'Tuần này · ' . $weekLabel, 'data' => $week],
        ['label' => 'Tháng này · ' . $monthLabel, 'data' => $month],
        ['label' => 'Quý này · ' . $quarterLabel, 'data' => $quarter],
    ] as $period): ?>
        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="text-secondary"><?= esc($period['label']) ?></div>
                    <div class="h3 fw-semibold mb-0"><?= esc(number_format($period['data']['revenue'], 0, ',', '.')) ?> ₫</div>
                    <div><?= esc((string) $period['data']['order_count']) ?> đơn hoàn tất</div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Top 5 sách bán chạy trong tuần này</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Sách</th><th>ISBN</th><th class="text-end">Đã bán</th><th class="text-end">Doanh thu</th></tr>
                    </thead>
                    <tbody>
                    <?php if ($topBooks === []): ?>
                        <tr><td class="text-center text-secondary py-4" colspan="4">Chưa có sách được bán trong tuần này.</td></tr>
                    <?php else: ?>
                        <?php foreach ($topBooks as $book): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($book['book_title']) ?></td>
                                <td><?= esc($book['isbn']) ?></td>
                                <td class="text-end"><?= esc((string) $book['quantity_sold']) ?></td>
                                <td class="text-end"><?= esc(number_format((float) $book['revenue'], 0, ',', '.')) ?> ₫</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Khung giờ bán được nhiều sách nhất tuần này</div>
            <div class="card-body">
                <?php if ($busiestHour === null): ?>
                    <p class="text-secondary mb-0">Chưa có đơn bán trong tuần này.</p>
                <?php else: ?>
                    <div class="display-6 fw-semibold peak-hour-value"><?= esc(str_pad((string) $busiestHour['sale_hour'], 2, '0', STR_PAD_LEFT)) ?>:00–<?= esc(str_pad((string) (((int) $busiestHour['sale_hour'] + 1) % 24), 2, '0', STR_PAD_LEFT)) ?>:00</div>
                    <p class="text-secondary mb-0"><?= esc((string) $busiestHour['quantity_sold']) ?> cuốn · <?= esc((string) $busiestHour['order_count']) ?> đơn</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Số sách bán theo giờ · tuần này</div>
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle mb-0">
                    <thead><tr><?php for ($hour = 0; $hour < 24; $hour++): ?><th class="text-center"><?= esc(str_pad((string) $hour, 2, '0', STR_PAD_LEFT)) ?>h</th><?php endfor; ?></tr></thead>
                    <tbody><tr><?php foreach ($hourTotals as $quantity): ?><td class="text-center"><?= esc((string) $quantity) ?></td><?php endforeach; ?></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
