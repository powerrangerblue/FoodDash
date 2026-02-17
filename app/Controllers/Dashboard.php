<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\DriverModel;
use App\Models\RestaurantModel;

class Dashboard extends BaseController
{
    public function admin()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized');
        }

        return view('dashboard/admin');
    }

    public function restaurant()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        if ($session->get('role') !== 'restaurant') {
            return redirect()->to('/login')->with('error', 'Unauthorized');
        }

        return view('dashboard/restaurant');
    }

    // Returns JSON used by admin dashboard (metrics, recent orders, chart data)
    public function adminData()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        $orderModel = new OrderModel();
        $driverModel = new DriverModel();
        $restaurantModel = new RestaurantModel();

        $todayStart = date('Y-m-d') . ' 00:00:00';
        $todayEnd   = date('Y-m-d') . ' 23:59:59';

        $totalOrdersToday = $orderModel->where('created_at >=', $todayStart)
            ->where('created_at <=', $todayEnd)
            ->countAllResults();

        $pendingOrders = $orderModel->where('status', 'pending')->countAllResults();

        $activeDeliveries = $orderModel->whereIn('status', ['assigned', 'out_for_delivery', 'on_the_way'])->countAllResults();

        $activeDrivers = $driverModel->where('is_active', 1)->countAllResults();

        $totalRestaurants = $restaurantModel->countAllResults();

        $dailyRevenue = (float) $orderModel->select('IFNULL(SUM(total_amount),0) as rev')
            ->where('created_at >=', $todayStart)
            ->where('created_at <=', $todayEnd)
            ->where('status', 'delivered')
            ->first()['rev'];

        // Recent orders (limit 20) with driver and restaurant names
        $builder = $orderModel->builder();
        $recent = $builder
            ->select('orders.id, order_number, customer_name, orders.restaurant_id, orders.driver_id, orders.status, orders.total_amount, orders.created_at, r.name as restaurant_name, d.name as driver_name')
            ->join('restaurants r', 'r.id = orders.restaurant_id', 'left')
            ->join('drivers d', 'd.id = orders.driver_id', 'left')
            ->orderBy('orders.created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        // Chart data - last 7 days
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $db = \Config\Database::connect();

        $ordersPerDay = $db->query("SELECT DATE(created_at) as day, COUNT(*) as count FROM orders WHERE created_at >= ? GROUP BY day ORDER BY day ASC", [$startDate . ' 00:00:00'])->getResultArray();
        $revenueTrends = $db->query("SELECT DATE(created_at) as day, IFNULL(SUM(total_amount),0) as total FROM orders WHERE created_at >= ? AND status = 'delivered' GROUP BY day ORDER BY day ASC", [$startDate . ' 00:00:00'])->getResultArray();

        $driverPerf = $db->query("SELECT d.name as driver, COUNT(o.id) as deliveries FROM orders o JOIN drivers d ON d.id = o.driver_id WHERE o.created_at >= ? GROUP BY d.id ORDER BY deliveries DESC LIMIT 8", [$startDate . ' 00:00:00'])->getResultArray();

        return $this->response->setJSON([
            'metrics' => [
                'totalOrdersToday' => (int) $totalOrdersToday,
                'activeDeliveries' => (int) $activeDeliveries,
                'activeDrivers'    => (int) $activeDrivers,
                'totalRestaurants' => (int) $totalRestaurants,
                'dailyRevenue'     => (float) $dailyRevenue,
                'pendingOrders'    => (int) $pendingOrders,
            ],
            'recentOrders' => $recent,
            'ordersPerDay' => $ordersPerDay,
            'revenueTrends' => $revenueTrends,
            'driverPerformance' => $driverPerf,
        ]);
    }

    // Update order status (AJAX)
    public function updateOrderStatus($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        $orderModel = new OrderModel();
        $allowed = ['pending', 'assigned', 'out_for_delivery', 'delivered', 'cancelled'];

        $status = $this->request->getPost('status');
        if (! in_array($status, $allowed)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid status']);
        }

        $order = $orderModel->find($id);
        if (! $order) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Order not found']);
        }

        $orderModel->update($id, ['status' => $status]);

        return $this->response->setJSON(['success' => true, 'status' => $status]);
    }
}
