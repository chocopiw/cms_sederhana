<?php
/**
 * CMS Framework MVC
 * Entry Point Application
 */

// Start session
session_start();

// Define base path
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Autoloader
spl_autoload_register(function ($class) {
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load configuration
require_once APP_PATH . '/config/config.php';

// Initialize Router
$router = new Core\Router();

// Define routes
$router->get('', 'HomeController@index');
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Dashboard routes
$router->get('/dashboard', 'DashboardController@index');
$router->get('/dashboard/posts', 'PostController@index');
$router->get('/dashboard/posts/create', 'PostController@create');
$router->post('/dashboard/posts/store', 'PostController@store');
$router->get('/dashboard/posts/edit/{id}', 'PostController@edit');
$router->post('/dashboard/posts/update/{id}', 'PostController@update');
$router->get('/dashboard/posts/delete/{id}', 'PostController@delete');

// Categories routes
$router->get('/dashboard/categories', 'CategoryController@index');
$router->get('/dashboard/categories/create', 'CategoryController@create');
$router->post('/dashboard/categories/store', 'CategoryController@store');
$router->get('/dashboard/categories/edit/{id}', 'CategoryController@edit');
$router->post('/dashboard/categories/update/{id}', 'CategoryController@update');
$router->get('/dashboard/categories/delete/{id}', 'CategoryController@delete');

// Users routes
$router->get('/dashboard/users', 'UserController@index');
$router->get('/dashboard/users/create', 'UserController@create');
$router->post('/dashboard/users/store', 'UserController@store');
$router->get('/dashboard/users/edit/{id}', 'UserController@edit');
$router->post('/dashboard/users/update/{id}', 'UserController@update');
$router->get('/dashboard/users/delete/{id}', 'UserController@delete');

// Public routes
$router->get('/posts', 'PublicController@posts');
$router->get('/posts/{id}', 'PublicController@showPost');
$router->get('/categories/{id}', 'PublicController@categoryPosts');

// Run the application
$router->run();
