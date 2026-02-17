<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterOrdersAddRelations extends Migration
{
    public function up()
    {
        $fields = [
            'restaurant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'driver_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', ['restaurant_id','driver_id']);
    }
}
