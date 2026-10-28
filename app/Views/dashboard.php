<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="dashboard-welcome mb-4">
    <div>
        <span class="dashboard-overline">BẢNG ĐIỀU KHIỂN</span>
        <h1 class="h3 mb-2">Tổng quan nhà sách</h1>
        <p class="text-secondary mb-0">Theo dõi hoạt động và quản lý cửa hàng của bạn tại một nơi.</p>
    </div>
</section>

<div class="row g-3 dashboard-metrics">
    <div class="col-md-4">
        <article class="card dashboard-card h-100">
            <div class="card-body">
                <div class="metric-heading">
                    <span class="metric-icon metric-icon-gold" aria-hidden="true">▦</span>
                    <span class="metric-label">Thể loại sách</span>
                </div>
                <div class="metric-value"><?= esc((string) $categoryCount) ?></div>
                <a class="metric-link" href="<?= site_url('categories') ?>">Quản lý thể loại <span aria-hidden="true">→</span></a>
            </div>
        </article>
    </div>
    <div class="col-md-4">
        <article class="card dashboard-card h-100">
            <div class="card-body">
                <div class="metric-heading">
                    <span class="metric-icon metric-icon-green" aria-hidden="true">▤</span>
                    <span class="metric-label">Đầu sách trong hệ thống</span>
                </div>
                <div class="metric-value"><?= esc((string) $bookCount) ?></div>
                <a class="metric-link" href="<?= site_url('books') ?>">Mở kho sách <span aria-hidden="true">→</span></a>
            </div>
        </article>
    </div>
    <div class="col-md-4">
        <article class="card dashboard-card h-100">
            <div class="card-body">
                <div class="metric-heading">
                    <span class="metric-icon metric-icon-rose" aria-hidden="true">!</span>
                    <span class="metric-label">Sách sắp hết hàng</span>
                </div>
                <div class="metric-value"><?= esc((string) $lowStockCount) ?></div>
                <a class="metric-link" href="<?= site_url('books') ?>">Kiểm tra tồn kho <span aria-hidden="true">→</span></a>
            </div>
        </article>
    </div>
</div>

<?php
$revenueValues = [];
if (is_array($dailyRevenue ?? null)) {
    foreach ($dailyRevenue as $revenue) {
        $revenueValues[] = (float) $revenue;
    }
}

$maxRevenue = $revenueValues !== [] ? max($revenueValues) : 0.0;
$weekRevenue = is_numeric($weekRevenue ?? null) ? (float) $weekRevenue : (float) array_sum($revenueValues);
$weekdayLabels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
?>
<section class="card dashboard-revenue mt-4" aria-labelledby="dashboard-revenue-title">
    <div class="card-body">
        <div class="dashboard-revenue-heading">
            <div>
                <span class="dashboard-revenue-overline">KẾT QUẢ KINH DOANH</span>
                <h2 id="dashboard-revenue-title">Doanh thu tuần này</h2>
                <p class="text-secondary mb-0">Từ thứ Hai đến Chủ nhật</p>
            </div>
            <div class="dashboard-revenue-total">
                <?= esc(number_format($weekRevenue, 0, ',', '.')) ?> ₫
            </div>
        </div>

        <div class="revenue-chart" role="img" aria-label="Biểu đồ doanh thu từng ngày trong tuần này">
            <?php foreach ($dailyRevenue as $date => $revenue): ?>
                <?php
                $numericRevenue = (float) $revenue;
                $dayIndex = is_string($date) && $date !== ''
                    ? ((int) (new DateTimeImmutable($date))->format('N') - 1)
                    : 0;
                $dayLabel = $weekdayLabels[$dayIndex] ?? '';
                $barHeight = $maxRevenue > 0 ? ($numericRevenue / $maxRevenue) * 100 : 0;
                ?>
                <div class="revenue-chart-day" title="<?= esc(($dayLabel !== '' ? $dayLabel . ': ' : '') . number_format($numericRevenue, 0, ',', '.') . ' ₫') ?>">
                    <span class="revenue-chart-value"><?= esc(number_format($numericRevenue, 0, ',', '.')) ?></span>
                    <div class="revenue-chart-track">
                        <div class="revenue-chart-bar" style="height: <?= esc((string) $barHeight) ?>%"></div>
                    </div>
                    <span class="revenue-chart-label"><?= esc($dayLabel) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <a class="metric-link dashboard-revenue-link" href="<?= site_url('reports') ?>">
            Xem báo cáo chi tiết <span aria-hidden="true">→</span>
        </a>
    </div>
</section>
<?= $this->endSection() ?>
