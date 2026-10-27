<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;

class SalesReportModel
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function revenueBetween(string $start, string $end): array
    {
        $row = $this->db->query(
            "SELECT COUNT(*) AS order_count, COALESCE(SUM(total_amount), 0) AS revenue
            FROM sales_orders
            WHERE status = 'completed' AND sold_at >= ? AND sold_at < ?",
            [$start, $end]
        )->getRowArray();

        return [
            'order_count' => (int) $row['order_count'],
            'revenue' => (float) $row['revenue'],
        ];
    }

    public function busiestHour(string $start, string $end): ?array
    {
        return $this->db->query(
            "SELECT HOUR(sold_at) AS sale_hour,
                SUM(sales_order_items.quantity) AS quantity_sold,
                COUNT(DISTINCT sales_orders.id) AS order_count
            FROM sales_orders
            INNER JOIN sales_order_items ON sales_order_items.order_id = sales_orders.id
            WHERE sales_orders.status = 'completed'
                AND sales_orders.sold_at >= ? AND sales_orders.sold_at < ?
            GROUP BY HOUR(sold_at)
            ORDER BY quantity_sold DESC, order_count DESC, sale_hour ASC
            LIMIT 1",
            [$start, $end]
        )->getRowArray() ?: null;
    }

    public function hourlySales(string $start, string $end): array
    {
        return $this->db->query(
            "SELECT HOUR(sold_at) AS sale_hour,
                SUM(sales_order_items.quantity) AS quantity_sold,
                COUNT(DISTINCT sales_orders.id) AS order_count
            FROM sales_orders
            INNER JOIN sales_order_items ON sales_order_items.order_id = sales_orders.id
            WHERE sales_orders.status = 'completed'
                AND sales_orders.sold_at >= ? AND sales_orders.sold_at < ?
            GROUP BY HOUR(sold_at)
            ORDER BY sale_hour ASC",
            [$start, $end]
        )->getResultArray();
    }

    public function topBooks(string $start, string $end, int $limit = 5): array
    {
        return $this->db->query(
            "SELECT sales_order_items.book_id, sales_order_items.book_title, sales_order_items.isbn,
                SUM(sales_order_items.quantity) AS quantity_sold,
                SUM(sales_order_items.line_total) AS revenue
            FROM sales_orders
            INNER JOIN sales_order_items ON sales_order_items.order_id = sales_orders.id
            WHERE sales_orders.status = 'completed'
                AND sales_orders.sold_at >= ? AND sales_orders.sold_at < ?
            GROUP BY sales_order_items.book_id, sales_order_items.book_title, sales_order_items.isbn
            ORDER BY quantity_sold DESC, revenue DESC, sales_order_items.book_title ASC
            LIMIT " . max(1, min($limit, 20)),
            [$start, $end]
        )->getResultArray();
    }
}
