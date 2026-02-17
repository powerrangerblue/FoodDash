<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));
        $twoDays = date('Y-m-d H:i:s', strtotime('-2 days'));

        $data = [
            [
                'order_number' => 'FD-1001',
                'customer_name' => 'Olivia Brown',
                'restaurant_id' => 1,
                'driver_id' => 1,
                'status' => 'delivered',
                'total_amount' => 24.50,
                'created_at' => $twoDays,
            ],
            [
                'order_number' => 'FD-1002',
                'customer_name' => 'Noah Smith',
                'restaurant_id' => 2,
                'driver_id' => 2,
                'status' => 'out_for_delivery',
                'total_amount' => 18.75,
                'created_at' => $yesterday,
            ],
            [
                'order_number' => 'FD-1003',
                'customer_name' => 'Emma Wilson',
                'restaurant_id' => 3,
                'driver_id' => null,
                'status' => 'pending',
                'total_amount' => 32.00,
                'created_at' => $now,
            ],
            [
                'order_number' => 'FD-1004',
                'customer_name' => 'Liam Johnson',
                'restaurant_id' => 1,
                'driver_id' => 3,
                'status' => 'assigned',
                'total_amount' => 12.00,
                'created_at' => $now,
            ],
        ];

        foreach ($data as $row) {
            $exists = $this->db->table('orders')->where('order_number', $row['order_number'])->countAllResults();
            if (! $exists) {
                $this->db->table('orders')->insert($row);
            }
        }
    }
}
