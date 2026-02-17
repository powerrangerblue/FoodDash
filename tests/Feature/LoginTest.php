<?php

use CodeIgniter\Test\FeatureTestCase;

final class LoginTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure test DB has users table (respect DB prefix)
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix;
        $table = $prefix . 'users';

        $db->query("CREATE TABLE IF NOT EXISTS {$table} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'restaurant',
            is_active INTEGER DEFAULT 1,
            reset_token TEXT,
            reset_expires TEXT,
            created_at TEXT,
            updated_at TEXT
        )");

        // Insert test users
        $hashAdmin = password_hash('AdminPass123', PASSWORD_DEFAULT);
        $hashRest  = password_hash('RestaurantPass123', PASSWORD_DEFAULT);

        $db->query("INSERT OR IGNORE INTO {$table} (email, password, role, is_active) VALUES
            ('admin@example.com', :a:, 'admin', 1),
            ('restaurant@example.com', :r:, 'restaurant', 1)", [
            'a' => $hashAdmin,
            'r' => $hashRest,
        ]);
    }

    public function testValidLoginRedirectsToRoleDashboard()
    {
        // Admin
        $result = $this->withHeaders(['Accept' => 'text/html'])->post('login', [
            'email' => 'admin@example.com',
            'password' => 'AdminPass123',
        ]);

        $result->assertRedirectTo('/dashboard/admin');

        // Restaurant
        $result = $this->withHeaders(['Accept' => 'text/html'])->post('login', [
            'email' => 'restaurant@example.com',
            'password' => 'RestaurantPass123',
        ]);

        $result->assertRedirectTo('/dashboard/restaurant');
    }

    public function testInvalidLoginShowsError()
    {
        $result = $this->post('login', [
            'email' => 'admin@example.com',
            'password' => 'WrongPassword',
        ]);

        $result->assertStatus(302);
        $this->assertTrue(session()->getFlashdata('error') !== null);
    }

    public function testAccessRestrictedWithoutLogin()
    {
        $result = $this->get('dashboard/admin');
        $result->assertRedirectTo('/login');
    }

    public function testSessionValidationAndLogout()
    {
        // Login
        $this->post('login', [
            'email' => 'admin@example.com',
            'password' => 'AdminPass123',
        ]);

        // Access dashboard
        $result = $this->get('dashboard/admin');
        $result->assertStatus(200);

        // Logout
        $this->get('logout');
        $result = $this->get('dashboard/admin');
        $result->assertRedirectTo('/login');
    }

    public function testSqlInjectionAttemptIsRejected()
    {
        $result = $this->post('login', [
            'email' => "' OR '1'='1",
            'password' => 'anything',
        ]);

        $result->assertStatus(302);
        $this->assertTrue(session()->getFlashdata('error') !== null);
    }
}
