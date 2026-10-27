<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('DashboardController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->setAutoRoute(false);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->post('logout', 'AuthController::logout', ['filter' => 'admin']);
$routes->get('member/register', 'MembersController::register');
$routes->post('member/register', 'MembersController::store');
$routes->get('member/login', 'MembersController::login');
$routes->post('member/login', 'MembersController::attemptLogin');
$routes->post('member/logout', 'MembersController::logout', ['filter' => 'member']);
$routes->get('/', 'DashboardController::index', ['filter' => 'admin']);

$routes->group('chat', ['filter' => 'chat'], static function (RouteCollection $routes): void {
    $routes->get('/', 'ChatController::index');
    $routes->get('messages', 'ChatController::messages');
    $routes->post('send', 'ChatController::send');
});

$routes->group('', ['filter' => 'admin'], static function (RouteCollection $routes): void {
    $routes->get('categories', 'CategoriesController::index');
    $routes->get('categories/create', 'CategoriesController::create');
    $routes->post('categories', 'CategoriesController::store');
    $routes->get('categories/(:num)/edit', 'CategoriesController::edit/$1');
    $routes->post('categories/(:num)', 'CategoriesController::update/$1');
    $routes->post('categories/(:num)/delete', 'CategoriesController::delete/$1');

    $routes->get('books', 'BooksController::index');
    $routes->get('books/create', 'BooksController::create');
    $routes->post('books', 'BooksController::store');
    $routes->get('books/(:num)/edit', 'BooksController::edit/$1');
    $routes->post('books/(:num)', 'BooksController::update/$1');
    $routes->post('books/(:num)/delete', 'BooksController::delete/$1');

    $routes->get('inventory', 'InventoryController::index');
    $routes->get('inventory/receive', 'InventoryController::receive');
    $routes->post('inventory/receive', 'InventoryController::store');

    $routes->get('sales', 'SalesController::index');
    $routes->get('sales/create', 'SalesController::create');
    $routes->post('sales', 'SalesController::store');

    $routes->get('reports', 'ReportsController::index');
});
