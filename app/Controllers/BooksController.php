<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class BooksController extends BaseController
{
    private function formData(array $book, array $errors, string $action, string $title): array
    {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->orderBy('name')->findAll();
        $categoryName = $book['category_name'] ?? '';

        if ($categoryName === '' && isset($book['category_id'])) {
            $category = $categoryModel->find((int) $book['category_id']);
            $categoryName = $category['name'] ?? '';
        }

        return [
            'book' => $book,
            'categories' => $categories,
            'categoryName' => $categoryName,
            'errors' => $errors,
            'action' => $action,
            'title' => $title,
        ];
    }

    public function index()
    {
        return view('books/index', [
            'books' => (new BookModel())->allWithCategories(),
        ]);
    }

    public function create()
    {
        return view('books/form', $this->formData([
            'category_name' => '',
            'title' => '',
            'author' => '',
            'isbn' => '',
            'publisher' => '',
            'publication_year' => '',
            'description' => '',
            'price' => '',
            'stock' => 0,
            'status' => 'active',
        ], [], site_url('books'), 'Thêm sách'));
    }

    public function store()
    {
        $data = $this->request->getPost([
            'category_name',
            'title',
            'author',
            'isbn',
            'publisher',
            'publication_year',
            'description',
            'price',
            'stock',
            'status',
        ]);
        $data['category_name'] = trim((string) $data['category_name']);

        if (! $this->validate($this->rules())) {
            return view('books/form', $this->formData(
                $data,
                $this->validator->getErrors(),
                site_url('books'),
                'Thêm sách'
            ));
        }

        $db = db_connect();
        $db->transBegin();
        $categoryId = $this->resolveCategoryId((string) $data['category_name']);
        if ($categoryId === null) {
            $db->transRollback();

            return view('books/form', $this->formData(
                $data,
                ['category_name' => 'Thể loại này đang ngừng sử dụng. Hãy nhập thể loại khác hoặc bật lại thể loại trong mục quản lý thể loại.'],
                site_url('books'),
                'Thêm sách'
            ));
        }

        $data['category_id'] = $categoryId;
        unset($data['category_name']);

        $bookModel = new BookModel();
        $bookModel->insert($data, true);
        $bookId = (int) $bookModel->getInsertID();

        if ((int) $data['stock'] > 0) {
            $db->table('book_stock_movements')->insert([
                'book_id' => $bookId,
                'admin_id' => (int) session()->get('admin_id'),
                'movement_type' => 'initial',
                'quantity' => (int) $data['stock'],
                'reference_key' => 'book-create:' . $bookId,
                'notes' => 'Tồn kho ban đầu khi tạo sách.',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \RuntimeException('Không thể tạo đầu sách và ghi nhận tồn kho ban đầu.');
        }

        $db->transCommit();

        return redirect()->to(site_url('books'))->with('success', 'Đã thêm sách.');
    }

    public function edit(int $id)
    {
        $book = (new BookModel())->find($id);
        if ($book === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('books/form', $this->formData(
            $book,
            [],
            site_url("books/{$id}"),
            'Chỉnh sửa sách'
        ));
    }

    public function update(int $id)
    {
        $model = new BookModel();
        $book = $model->find($id);
        if ($book === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost([
            'category_name',
            'title',
            'author',
            'isbn',
            'publisher',
            'publication_year',
            'description',
            'price',
            'status',
        ]);
        $data['category_name'] = trim((string) $data['category_name']);

        if (! $this->validate($this->rules($id))) {
            return view('books/form', $this->formData(
                array_merge($book, $data),
                $this->validator->getErrors(),
                site_url("books/{$id}"),
                'Chỉnh sửa sách'
            ));
        }

        $db = db_connect();
        $db->transBegin();
        $categoryId = $this->resolveCategoryId((string) $data['category_name'], (int) $book['category_id']);
        if ($categoryId === null) {
            $db->transRollback();

            return view('books/form', $this->formData(
                array_merge($book, $data),
                ['category_name' => 'Thể loại này đang ngừng sử dụng. Hãy nhập thể loại khác hoặc bật lại thể loại trong mục quản lý thể loại.'],
                site_url("books/{$id}"),
                'Chỉnh sửa sách'
            ));
        }

        $data['category_id'] = $categoryId;
        unset($data['category_name']);
        $model->update($id, $data);
        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \RuntimeException('Không thể cập nhật thông tin sách.');
        }
        $db->transCommit();

        return redirect()->to(site_url('books'))->with('success', 'Đã cập nhật sách.');
    }

    public function delete(int $id)
    {
        $model = new BookModel();
        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($model->hasSales($id) || $model->hasStockMovements($id)) {
            return redirect()->to(site_url('books'))
                ->with('error', 'Không thể xóa sách đã có lịch sử kho hoặc bán hàng. Hãy chuyển trạng thái sang ngừng bán.');
        }

        $model->delete($id);

        return redirect()->to(site_url('books'))->with('success', 'Đã xóa sách.');
    }

    private function rules(?int $ignoreId = null): array
    {
        $isbnRule = 'required|max_length[20]|alpha_numeric_punct|is_unique[books.isbn]';
        if ($ignoreId !== null) {
            $isbnRule = "required|max_length[20]|alpha_numeric_punct|is_unique[books.isbn,id,{$ignoreId}]";
        }

        $rules = [
            'category_name' => 'required|min_length[2]|max_length[100]',
            'title' => 'required|min_length[2]|max_length[200]',
            'author' => 'required|min_length[2]|max_length[150]',
            'isbn' => $isbnRule,
            'publisher' => 'permit_empty|max_length[150]',
            'publication_year' => 'permit_empty|is_natural|greater_than_equal_to[0]|less_than_equal_to[' . date('Y') . ']',
            'description' => 'permit_empty|max_length[2000]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if ($ignoreId === null) {
            $rules['stock'] = 'required|is_natural';
        }

        return $rules;
    }

    private function resolveCategoryId(string $name, ?int $currentCategoryId = null): ?int
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('name', trim($name))->first();

        if ($category !== null) {
            if ($category['status'] !== 'active' && (int) $category['id'] !== $currentCategoryId) {
                return null;
            }

            return (int) $category['id'];
        }

        $categoryId = $categoryModel->insert([
            'name' => trim($name),
            'description' => null,
            'status' => 'active',
        ], true);

        return $categoryId === false ? null : (int) $categoryId;
    }
}
