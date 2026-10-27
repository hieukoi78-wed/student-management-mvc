<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Quản trị nhà sách') ?></title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('bookstore-theme');
            document.documentElement.dataset.theme = savedTheme || 'light';
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>?v=<?= esc((string) filemtime(FCPATH . 'css/style.css')) ?>">
</head>
<body class="admin-page">
<?php
$isAdmin = session()->get('admin_id') !== null;
$userName = $isAdmin ? (session()->get('admin_name') ?: 'Quản trị viên') : (session()->get('member_name') ?: 'Thành viên');
$currentPath = trim(uri_string(), '/');
$navigation = $isAdmin ? [
    ['label' => 'Tổng quan', 'path' => '', 'url' => site_url('/'), 'icon' => '⌂'],
    ['label' => 'Sách', 'path' => 'books', 'url' => site_url('books'), 'icon' => '▤'],
    ['label' => 'Thể loại', 'path' => 'categories', 'url' => site_url('categories'), 'icon' => '▦'],
    ['label' => 'Nhập kho', 'path' => 'inventory', 'url' => site_url('inventory'), 'icon' => '↙'],
    ['label' => 'Bán hàng', 'path' => 'sales', 'url' => site_url('sales'), 'icon' => '＋'],
    ['label' => 'Báo cáo', 'path' => 'reports', 'url' => site_url('reports'), 'icon' => '▥'],
    ['label' => 'Tin nhắn', 'path' => 'chat', 'url' => site_url('chat'), 'icon' => '◌'],
] : [
    ['label' => 'Phòng chat', 'path' => 'chat', 'url' => site_url('chat'), 'icon' => '◌'],
];
$logoutUrl = $isAdmin ? site_url('logout') : site_url('member/logout');
?>
<div class="admin-shell">
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <aside class="admin-sidebar" id="admin-sidebar" aria-label="Điều hướng chính">
        <a class="brand-lockup" href="<?= site_url('/') ?>">
            <span class="brand-mark" aria-hidden="true">N</span>
            <span class="brand-copy">
                <strong>NHÀ SÁCH</strong>
                <small>BOOKSTORE MANAGEMENT</small>
            </span>
        </a>

        <div class="sidebar-caption"><?= $isAdmin ? 'KHÔNG GIAN QUẢN LÝ' : 'CỘNG ĐỒNG NHÀ SÁCH' ?></div>
        <nav class="sidebar-nav">
            <?php foreach ($navigation as $item): ?>
                <?php $isActive = $item['path'] === '' ? $currentPath === '' : ($currentPath === $item['path'] || str_starts_with($currentPath, $item['path'] . '/')); ?>
                <a class="sidebar-link <?= $isActive ? 'active' : '' ?>" href="<?= esc($item['url']) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <span class="sidebar-icon" aria-hidden="true"><?= esc($item['icon']) ?></span>
                    <span><?= esc($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="sidebar-toggle" id="sidebar-toggle" type="button" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Mở menu">
                <span></span><span></span><span></span>
            </button>
            <div class="topbar-context">
                <span class="topbar-eyebrow">NHÀ SÁCH <span>/</span> <?= $isAdmin ? 'QUẢN TRỊ' : 'CỘNG ĐỒNG' ?></span>
                <strong>Chào mừng trở lại</strong>
            </div>
            <div class="topbar-right">
                <button class="theme-toggle" id="theme-toggle" type="button" aria-label="Chuyển sang giao diện tối" aria-pressed="false">
                    <span class="theme-toggle-icon" aria-hidden="true">☾</span>
                    <span class="theme-toggle-label">Giao diện tối</span>
                </button>
                <div class="topbar-date"><?= esc(date('d/m/Y')) ?></div>
                <div class="topbar-account">
                    <span class="account-avatar"><?= esc(mb_strtoupper(mb_substr($userName, 0, 1))) ?></span>
                    <span class="account-copy">
                        <strong><?= esc($userName) ?></strong>
                        <small><?= $isAdmin ? 'Quản trị viên' : 'Thành viên' ?></small>
                    </span>
                </div>
                <form class="topbar-logout" method="post" action="<?= esc($logoutUrl) ?>">
                    <?= csrf_field() ?>
                    <button class="topbar-logout-button" type="submit">Đăng xuất</button>
                </form>
            </div>
        </header>

        <main class="admin-content">
            <?php if ($message = session()->getFlashdata('success')): ?>
                <div class="alert alert-success app-alert" role="status"><?= esc($message) ?></div>
            <?php endif; ?>
            <?php if ($message = session()->getFlashdata('error')): ?>
                <div class="alert alert-danger app-alert" role="alert"><?= esc($message) ?></div>
            <?php endif; ?>
            <?= $this->renderSection('content') ?>
            <footer class="admin-footer">
                <span>© <?= esc(date('Y')) ?> Hệ thống nhà sách</span>
            </footer>
        </main>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
<script>
(() => {
    const body = document.body;
    const toggle = document.getElementById('sidebar-toggle');
    const backdrop = document.getElementById('sidebar-backdrop');
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle.querySelector('.theme-toggle-icon');
    const themeLabel = themeToggle.querySelector('.theme-toggle-label');
    const root = document.documentElement;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const updateThemeButton = () => {
        const isDark = root.dataset.theme === 'dark';
        themeToggle.setAttribute('aria-pressed', String(isDark));
        themeToggle.setAttribute('aria-label', isDark ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối');
        themeIcon.textContent = isDark ? '☀' : '☾';
        themeLabel.textContent = isDark ? 'Giao diện sáng' : 'Giao diện tối';
    };

    updateThemeButton();
    themeToggle.addEventListener('click', () => {
        const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        root.dataset.theme = nextTheme;
        localStorage.setItem('bookstore-theme', nextTheme);
        updateThemeButton();
    });

    const closeSidebar = () => {
        body.classList.remove('sidebar-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Mở menu');
    };

    toggle.addEventListener('click', () => {
        const isOpen = body.classList.toggle('sidebar-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Đóng menu' : 'Mở menu');
    });
    backdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeSidebar();
    });

    const initializePageMotion = () => {
        if (reducedMotion || !window.gsap) return;

        if (window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);

        const content = document.querySelector('.admin-content');
        gsap.from(content, {y: 12, duration: 0.42, ease: 'power2.out'});

        if (window.ScrollTrigger) {
            gsap.utils.toArray('.admin-content .card').forEach((card) => {
                gsap.from(card, {
                    y: 18,
                    duration: 0.45,
                    ease: 'power2.out',
                    scrollTrigger: {trigger: card, start: 'top 92%', once: true},
                });
            });
        }

        document.querySelectorAll('.sidebar-link').forEach((link) => {
            link.addEventListener('click', (event) => {
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank') return;
                event.preventDefault();
                const destination = link.href;
                closeSidebar();
                gsap.to(content, {
                    autoAlpha: 0,
                    x: -12,
                    duration: 0.16,
                    ease: 'power1.in',
                    onComplete: () => window.location.assign(destination),
                });
            });
        });
    };

    if (document.readyState === 'complete') initializePageMotion();
    else window.addEventListener('load', initializePageMotion, {once: true});
})();
</script>
</body>
</html>
