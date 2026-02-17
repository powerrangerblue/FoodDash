<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Carlos M.', 'phone' => '555-0101'],
            ['name' => 'Aisha R.', 'phone' => '555-0133'],
            ['name' => 'Ravi P.', 'phone' => '555-0177'],
        ];

        foreach ($data as $row) {
            $this->db->table('drivers')->insert($row);
        }
    }
}
