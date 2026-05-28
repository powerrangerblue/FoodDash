<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminMfaAndSecurityPermissions extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('permissions')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $permissions = [
            [
                'permission_key' => 'manage_admin_mfa',
                'label' => 'MFA Settings',
                'module' => 'Admin',
                'description' => 'Open and update the admin MFA settings page',
                'sort_order' => 70,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'permission_key' => 'view_security_monitor',
                'label' => 'Security Monitor',
                'module' => 'Admin',
                'description' => 'Open the security monitoring page and reports',
                'sort_order' => 80,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($permissions as $permission) {
            $existing = $this->db->table('permissions')
                ->where('permission_key', $permission['permission_key'])
                ->get()
                ->getRowArray();

            if ($existing) {
                $this->db->table('permissions')
                    ->where('permission_key', $permission['permission_key'])
                    ->update($permission);
            } else {
                $this->db->table('permissions')->insert($permission);
            }
        }

        if (! $this->db->tableExists('roles') || ! $this->db->tableExists('role_permissions')) {
            return;
        }

        $adminRole = $this->db->table('roles')
            ->groupStart()
            ->where('slug', 'admin')
            ->orWhere('scope', 'admin')
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (! $adminRole) {
            return;
        }

        $roleId = (int) $adminRole['id'];
        $permissionRows = $this->db->table('permissions')
            ->select('id')
            ->whereIn('permission_key', ['manage_admin_mfa', 'view_security_monitor'])
            ->get()
            ->getResultArray();

        foreach ($permissionRows as $row) {
            $permissionId = (int) ($row['id'] ?? 0);
            if ($permissionId <= 0) {
                continue;
            }

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
    }

    public function down()
    {
        if (! $this->db->tableExists('permissions')) {
            return;
        }

        $keys = ['manage_admin_mfa', 'view_security_monitor'];
        foreach ($keys as $key) {
            $permission = $this->db->table('permissions')->where('permission_key', $key)->get()->getRowArray();
            if ($permission && $this->db->tableExists('role_permissions')) {
                $this->db->table('role_permissions')->where('permission_id', (int) $permission['id'])->delete();
            }
            if ($permission) {
                $this->db->table('permissions')->where('id', (int) $permission['id'])->delete();
            }
        }
    }
}