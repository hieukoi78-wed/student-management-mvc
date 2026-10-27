<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\SalesOrderModel;

class SalesController extends BaseController
{
    public function index()
    {
        return view('sales/index', [
            'orders' => (new SalesOrderModel())->recentWithAdmin(),
        ]);
    }

    public function create()
    {
        return view('sales/create', [
            'books' => (new BookModel())
                ->where('status', 'active')
                ->where('stock >', 0)
                ->orderBy('title')
                ->findAll(),
            'errors' => session()->getFlashdata('errors') ?? [],
            'old' => session()->getFlashdata('old') ?? [],
        ]);
    }

    public function store()
    {
        $bookIds = $this->request->getPost('book_id');
        $quantities = $this->request->getPost('quantity');
        $customerName = trim((string) $this->request->getPost('customer_name'));
        $old = [
            'book_id' => is_array($bookIds) ? $bookIds : [],
            'quantity' => is_array($quantities) ? $quantities : [],
            'customer_name' => $customerName,
        ];

        if (! is_array($bookIds) || ! is_array($quantities) || count($bookIds) !== count($quantities) || count($bookIds) === 0 || count($bookIds) > 30) {
            return redirect()->to(site_url('sales/create'))
                ->with('errors', ['items' => 'Đơn hàng cần có từ 1 đến 30 dòng sách hợp lệ.'])
                ->with('old', $old);
        }

        if (mb_strlen($customerName) > 120) {
            return redirect()->to(site_url('sales/create'))
                ->with('errors', ['customer_name' => 'Tên khách hàng tối đa 120 ký tự.'])
                ->with('old', $old);
        }

        $items = [];
        foreach ($bookIds as $index => $bookId) {
            $id = filter_var($bookId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $quantity = filter_var($quantities[$index] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 1000000]]);

            if ($id === false || $quantity === false) {
                return redirect()->to(site_url('sales/create'))
                    ->with('errors', ['items' => 'Mỗi dòng phải chọn sách và số lượng lớn hơn 0.'])
                    ->with('old', $old);
            }

            $items[$id] = ($items[$id] ?? 0) + $quantity;
        }

        if (count($items) > 30 || max($items) > 1000000) {
            return redirect()->to(site_url('sales/create'))
                ->with('errors', ['items' => 'Số dòng hoặc số lượng sách vượt quá giới hạn.'])
                ->with('old', $old);
        }

        ksort($items, SORT_NUMERIC);
        $db = db_connect();
        $db->transBegin();

        $bookRows = [];
        foreach ($items as $bookId => $quantity) {
            $book = $db->query(
                'SELECT id, title, isbn, price, stock, status FROM books WHERE id = ? FOR UPDATE',
                [$bookId]
            )->getRowArray();

            if ($book === null || $book['status'] !== 'active') {
                $db->transRollback();

                return redirect()->to(site_url('sales/create'))
                    ->with('errors', ['items' => 'Có sách không còn được bán. Vui lòng tải lại trang và thử lại.'])
                    ->with('old', $old);
            }

            if ((int) $book['stock'] < $quantity) {
                $db->transRollback();

                return redirect()->to(site_url('sales/create'))
                    ->with('errors', ['items' => 'Sách "' . $book['title'] . '" chỉ còn ' . $book['stock'] . ' cuốn trong kho.'])
                    ->with('old', $old);
            }

            $bookRows[$bookId] = $book;
        }

        $total = 0.0;
        foreach ($items as $bookId => $quantity) {
            $total += (float) $bookRows[$bookId]['price'] * $quantity;
        }

        $soldAt = date('Y-m-d H:i:s');
        $orderNumber = 'NS-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $db->table('sales_orders')->insert([
            'order_number' => $orderNumber,
            'admin_id' => (int) session()->get('admin_id'),
            'customer_name' => $customerName !== '' ? $customerName : null,
            'total_amount' => number_format($total, 2, '.', ''),
            'status' => 'completed',
            'sold_at' => $soldAt,
            'created_at' => $soldAt,
            'updated_at' => $soldAt,
        ]);
        $orderId = (int) $db->insertID();

        foreach ($items as $bookId => $quantity) {
            $book = $bookRows[$bookId];
            $lineTotal = (float) $book['price'] * $quantity;

            $db->table('sales_order_items')->insert([
                'order_id' => $orderId,
                'book_id' => $bookId,
                'book_title' => $book['title'],
                'isbn' => $book['isbn'],
                'quantity' => $quantity,
                'unit_price' => $book['price'],
                'line_total' => number_format($lineTotal, 2, '.', ''),
            ]);
            $db->table('books')->where('id', $bookId)->set('stock', 'stock - ' . $quantity, false)->update();
            $db->table('book_stock_movements')->insert([
                'book_id' => $bookId,
                'admin_id' => (int) session()->get('admin_id'),
                'movement_type' => 'sold',
                'quantity' => $quantity,
                'reference_key' => 'sale:' . $orderId . ':' . $bookId,
                'notes' => 'Đơn hàng ' . $orderNumber,
                'created_at' => $soldAt,
            ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \RuntimeException('Không thể lưu đơn bán. Không có thay đổi tồn kho nào được áp dụng.');
        }

        $db->transCommit();

        return redirect()->to(site_url('sales'))->with('success', 'Đã tạo đơn bán ' . $orderNumber . '.');
    }
}
