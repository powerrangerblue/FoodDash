<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');

// Authentication
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// Password recovery
$routes->get('forgot', 'Auth::forgot');
$routes->post('forgot', 'Auth::sendReset');
$routes->get('reset/(:any)', 'Auth::reset/$1');
$routes->post('reset/(:any)', 'Auth::resetPassword/$1');

// Dashboards (protected by filter)
$routes->get('dashboard/admin', 'Dashboard::admin');
$routes->get('dashboard/restaurant', 'Dashboard::restaurant');

// Admin data endpoints
$routes->get('dashboard/admin/data', 'Dashboard::adminData');
$routes->get('dashboard/restaurant/data', 'Dashboard::restaurantData');
$routes->post('dashboard/order/(:num)/status', 'Dashboard::updateOrderStatus/$1');

// Menu Items (Restaurant)
$routes->get('menu', 'MenuItems::index');
$routes->get('menu/create', 'MenuItems::create');
$routes->post('menu/store', 'MenuItems::store');
$routes->get('menu/(:num)/edit', 'MenuItems::edit/$1');
$routes->post('menu/(:num)/update', 'MenuItems::update/$1');
$routes->post('menu/(:num)/delete', 'MenuItems::delete/$1');
$routes->post('menu/(:num)/toggle', 'MenuItems::toggleAvailability/$1');

// Orders
$routes->get('orders', 'Orders::restaurantOrders');
$routes->post('orders/(:num)/status', 'Orders::updateRestaurantOrderStatus/$1');
$routes->post('orders/(:num)/assign-driver', 'Orders::assignDriver/$1');
$routes->get('api/orders/daily-sales', 'Orders::getDailySales');

// Admin Management
$routes->get('admin/users', 'AdminManagement::users');
$routes->post('admin/users/(:num)/suspend', 'AdminManagement::suspendUser/$1');
$routes->post('admin/users/(:num)/activate', 'AdminManagement::activateUser/$1');

$routes->get('admin/restaurants/pending', 'AdminManagement::pendingRestaurants');
$routes->post('admin/restaurants/(:num)/approve', 'AdminManagement::approveRestaurant/$1');
$routes->post('admin/restaurants/(:num)/reject', 'AdminManagement::rejectRestaurant/$1');

$routes->get('admin/drivers/pending', 'AdminManagement::pendingDrivers');
$routes->post('admin/drivers/(:num)/approve', 'AdminManagement::approveDriver/$1');
$routes->post('admin/drivers/(:num)/reject', 'AdminManagement::rejectDriver/$1');

$routes->get('api/admin/revenue-summary', 'AdminManagement::getRevenueSummary');
