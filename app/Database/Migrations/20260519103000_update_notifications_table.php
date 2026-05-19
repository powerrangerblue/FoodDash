<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateNotificationsTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('notifications')) {
            return;
        }

        $fields = $this->db->getFieldNames('notifications');

        $addColumns = [];

        if (! in_array('user_id', $fields, true) && $this->db->fieldExists('customer_id', 'notifications')) {
            $addColumns['user_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'customer_id',
            ];
        }

        if (! in_array('order_id', $fields, true)) {
            $addColumns['order_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => in_array('user_id', $fields, true) ? 'user_id' : 'customer_id',
            ];
        }

        if (! in_array('is_read', $fields, true)) {
            $addColumns['is_read'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'type',
            ];
        }

        if (! in_array('is_deleted', $fields, true)) {
            $addColumns['is_deleted'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'is_read',
            ];
        }

        if (! in_array('created_at', $fields, true)) {
            $addColumns['created_at'] = [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'is_deleted',
            ];
        }

        if (! in_array('updated_at', $fields, true)) {
            $addColumns['updated_at'] = [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => in_array('created_at', $fields, true) ? 'created_at' : 'is_deleted',
            ];
        }

        if (! empty($addColumns)) {
            $this->forge->addColumn('notifications', $addColumns);
        }

        if (
            $this->db->fieldExists('user_id', 'notifications')
            && $this->db->fieldExists('customer_id', 'notifications')
        ) {
            $this->db->query('UPDATE notifications SET user_id = customer_id WHERE user_id IS NULL AND customer_id IS NOT NULL');
        }

        if ($this->db->fieldExists('is_deleted', 'notifications')) {
            $this->db->query('UPDATE notifications SET is_deleted = 0 WHERE is_deleted IS NULL');
        }

        if ($this->db->fieldExists('is_read', 'notifications')) {
            $this->db->query('UPDATE notifications SET is_read = 0 WHERE is_read IS NULL');
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('notifications')) {
            return;
        }

        $dropColumns = [];

        foreach (['user_id', 'order_id', 'is_deleted'] as $column) {
            if ($this->db->fieldExists($column, 'notifications')) {
                $dropColumns[] = $column;
            }
        }

        if (! empty($dropColumns)) {
            $this->forge->dropColumn('notifications', $dropColumns);
        }
    }
}