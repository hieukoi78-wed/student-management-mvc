<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderModel extends Model
{
    protected $table = 'sales_orders';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'order_number',
        'admin_id',
        'customer_name',
        'total_amount',
        'status',
        'sold_at',
    ];
    protected $useTimestamps = true;

    public function recentWithAdmin(int $limit = 100): array
    {
        return $this->select('sales_orders.*, admins.full_name AS admin_name')
            ->join('admins', 'admins.id = sales_orders.admin_id')
            ->orderBy('sales_orders.sold_at', 'DESC')
            ->findAll($limit);
    }
}
