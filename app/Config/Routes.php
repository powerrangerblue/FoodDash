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
$routes->post('dashboard/order/(:num)/status', 'Dashboard::updateOrderStatus/$1');
