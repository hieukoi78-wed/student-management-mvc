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
<?= $this->endSection() ?>
