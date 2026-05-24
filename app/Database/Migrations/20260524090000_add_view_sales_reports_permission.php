<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddViewSalesReportsPermission extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('permissions')) {
            return;
        }

        $permissionKey = 'view_sales_reports';
        $now = date('Y-m-d H:i:s');
        $payload = [
            'permission_key' => $permissionKey,
            'label' => 'Sales',
            'module' => 'Restaurant',
            'description' => 'Open the restaurant sales dashboard',
            'sort_order' => 75,
            'updated_at' => $now,
        ];

        $existing = $this->db->table('permissions')
            ->where('permission_key', $permissionKey)
            ->get()
            ->getRowArray();

        if ($existing) {
            $this->db->table('permissions')->where('permission_key', $permissionKey)->update($payload);
        } else {
            $payload['created_at'] = $now;
            $this->db->table('permissions')->insert($payload);
        }

        if (! $this->db->tableExists('roles') || ! $this->db->tableExists('role_permissions')) {
            return;
        }

        $permissionRow = $this->db->table('permissions')
            ->select('id')
            ->where('permission_key', $permissionKey)
            ->get()
            ->getRowArray();

        if (! $permissionRow) {
            return;
        }

        $role = $this->db->table('roles')
            ->groupStart()
            ->where('slug', 'restaurant')
            ->orWhere('scope', 'restaurant')
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (! $role) {
            return;
        }

        $roleId = (int) $role['id'];

        if ((string) ($role['name'] ?? '') !== 'Restaurant Owner' || (string) ($role['description'] ?? '') !== 'Default restaurant owner role with full restaurant access') {
            $this->db->table('roles')->where('id', $roleId)->update([
                'name' => 'Restaurant Owner',
                'description' => 'Default restaurant owner role with full restaurant access',
                'updated_at' => $now,
            ]);
        }

        $permissionId = (int) $permissionRow['id'];

        $exists = $this->db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('role_permissions')->insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('permissions')) {
            return;
        }

        $permission = $this->db->table('permissions')
            ->where('permission_key', 'view_sales_reports')
            ->get()
            ->getRowArray();

        if ($permission && $this->db->tableExists('role_permissions')) {
            $this->db->table('role_permissions')->where('permission_id', (int) $permission['id'])->delete();
        }

        if ($permission) {
            $this->db->table('permissions')->where('id', (int) $permission['id'])->delete();
        }
    }
}