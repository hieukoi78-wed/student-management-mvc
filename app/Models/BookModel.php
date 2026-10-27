<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'category_id',
        'title',
        'author',
        'isbn',
        'publisher',
        'publication_year',
        'description',
        'price',
        'stock',
        'status',
    ];
    protected $useTimestamps = true;

    public function allWithCategories(): array
    {
        return $this->select('books.*, categories.name AS category_name')
            ->join('categories', 'categories.id = books.category_id')
            ->orderBy('books.id', 'DESC')
            ->findAll();
    }

    public function hasSales(int $bookId): bool
    {
        return db_connect()->table('sales_order_items')
            ->where('book_id', $bookId)
            ->countAllResults() > 0;
    }

    public function hasStockMovements(int $bookId): bool
    {
        return db_connect()->table('book_stock_movements')
            ->where('book_id', $bookId)
            ->countAllResults() > 0;
    }
}
