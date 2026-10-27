<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">Quản lý sách</h1>
        <p class="text-secondary mb-0">Quản lý đầu sách, tác giả, giá bán và tồn kho.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url('books/create') ?>">Thêm sách</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Tên sách</th>
                    <th scope="col">Tác giả</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Thể loại</th>
                    <th scope="col" class="text-end">Giá</th>
                    <th scope="col" class="text-end">Tồn kho</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col" class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($books === []): ?>
                <tr><td class="text-center text-secondary py-4" colspan="8">Chưa có sách nào.</td></tr>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($book['title']) ?></td>
                        <td><?= esc($book['author']) ?></td>
                        <td><?= esc($book['isbn']) ?></td>
                        <td><?= esc($book['category_name']) ?></td>
                        <td class="text-end"><?= esc(number_format((float) $book['price'], 0, ',', '.')) ?> ₫</td>
                        <td class="text-end"><?= esc((string) $book['stock']) ?></td>
                        <td><span class="badge <?= $book['status'] === 'active' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $book['status'] === 'active' ? 'Đang bán' : 'Ngừng bán' ?></span></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url('books/' . $book['id'] . '/edit') ?>">Sửa</a>
                            <form class="d-inline" method="post" action="<?= site_url('books/' . $book['id'] . '/delete') ?>" onsubmit="return confirm('Bạn có chắc muốn xóa sách này?')">
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
