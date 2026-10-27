<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'description', 'status'];
    protected $useTimestamps = true;

    public function hasBooks(int $categoryId): bool
    {
        return db_connect()->table('books')
            ->where('category_id', $categoryId)
            ->countAllResults() > 0;
    }
}
