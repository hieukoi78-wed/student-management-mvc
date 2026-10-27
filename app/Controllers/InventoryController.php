<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\StockMovementModel;

class InventoryController extends BaseController
{
    public function index()
    {
        return view('inventory/index', [
            'movements' => (new StockMovementModel())->recentWithBooks(),
        ]);
    }

    public function receive()
    {
        $old = session()->getFlashdata('old') ?? [];
        $selectedBookId = $old['book_id'] ?? $this->request->getGet('book_id') ?? '';
        $selectedBookId = filter_var($selectedBookId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: '';

        return view('inventory/receive', [
            'books' => (new BookModel())->orderBy('title')->findAll(),
            'errors' => session()->getFlashdata('errors') ?? [],
            'old' => $old,
            'selectedBookId' => (string) $selectedBookId,
        ]);
    }

    public function store()
    {
        $data = [
            'book_id' => $this->request->getPost('book_id'),
            'quantity' => $this->request->getPost('quantity'),
            'notes' => trim((string) $this->request->getPost('notes')),
        ];
        $rules = [
            'book_id' => 'required|is_natural_no_zero|is_not_unique[books.id]',
            'quantity' => 'required|is_natural_no_zero|less_than_equal_to[1000000]',
            'notes' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('inventory/receive'))
                ->with('errors', $this->validator->getErrors())
                ->with('old', $data);
        }

        $db = db_connect();
        $quantity = (int) $data['quantity'];
        $bookId = (int) $data['book_id'];

        $db->transBegin();
        $book = $db->query(
            'SELECT id, stock FROM books WHERE id = ? FOR UPDATE',
            [$bookId]
        )->getRowArray();

        if ($book === null) {
            $db->transRollback();

            return redirect()->to(site_url('inventory/receive'))
                ->with('errors', ['book_id' => 'Không tìm thấy đầu sách đã chọn.'])
                ->with('old', $data);
        }

        if ((int) $book['stock'] + $quantity > 4294967295) {
            $db->transRollback();

            return redirect()->to(site_url('inventory/receive'))
                ->with('errors', ['quantity' => 'Số lượng nhập vượt quá giới hạn tồn kho.'])
                ->with('old', $data);
        }

        $db->table('books')->where('id', $bookId)->set('stock', 'stock + ' . $quantity, false)->update();
        $db->table('book_stock_movements')->insert([
            'book_id' => $bookId,
            'admin_id' => (int) session()->get('admin_id'),
            'movement_type' => 'received',
            'quantity' => $quantity,
            'reference_key' => null,
            'notes' => $data['notes'] !== '' ? $data['notes'] : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \RuntimeException('Không thể ghi nhận phiếu nhập kho.');
        }

        $db->transCommit();

        return redirect()->to(site_url('inventory'))
            ->with('success', 'Đã nhập thêm ' . number_format($quantity) . ' cuốn sách vào kho.');
    }
}
