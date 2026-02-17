<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Sunrise Pizza', 'address' => '123 Main St'],
            ['name' => 'Green Bowl', 'address' => '456 Market Ave'],
            ['name' => 'Sushi Central', 'address' => '789 Ocean Rd'],
        ];

        foreach ($data as $row) {
            $this->db->table('restaurants')->insert($row);
        }
    }
}
