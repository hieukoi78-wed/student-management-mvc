<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">Thể loại sách</h1>
        <p class="text-secondary mb-0">Thêm, sửa và xóa thể loại của nhà sách.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url('categories/create') ?>">Thêm danh mục</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Tên thể loại</th>
                    <th scope="col">Mô tả</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col" class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($categories === []): ?>
                <tr><td class="text-center text-secondary py-4" colspan="4">Chưa có thể loại sách nào.</td></tr>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($category['name']) ?></td>
                        <td><?= esc($category['description'] ?? '') ?></td>
                        <td><span class="badge <?= $category['status'] === 'active' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $category['status'] === 'active' ? 'Đang dùng' : 'Ngừng dùng' ?></span></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url('categories/' . $category['id'] . '/edit') ?>">Sửa</a>
                            <form class="d-inline" method="post" action="<?= site_url('categories/' . $category['id'] . '/delete') ?>" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
