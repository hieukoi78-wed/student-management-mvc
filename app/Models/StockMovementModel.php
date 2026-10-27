<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'book_stock_movements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'book_id',
        'admin_id',
        'movement_type',
        'quantity',
        'reference_key',
        'notes',
        'created_at',
    ];
    protected $useTimestamps = false;

    public function recentWithBooks(int $limit = 50): array
    {
        return $this->select('book_stock_movements.*, books.title, books.isbn, admins.full_name AS admin_name')
            ->join('books', 'books.id = book_stock_movements.book_id')
            ->join('admins', 'admins.id = book_stock_movements.admin_id', 'left')
            ->orderBy('book_stock_movements.id', 'DESC')
            ->findAll($limit);
    }
}
