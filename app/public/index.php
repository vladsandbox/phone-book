<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$router = new Router();

// Маршруты
$router->add('GET', '/', 'HomeController', 'index');
$router->add('GET', '/register', 'AuthController', 'showRegister');
$router->add('GET', '/login', 'AuthController', 'showLogin');
$router->add('POST', '/api/register', 'AuthController', 'register');
$router->add('POST', '/api/login', 'AuthController', 'login');
$router->add('GET', '/logout', 'AuthController', 'logout');

$router->add('POST', '/api/contacts', 'ContactController', 'create');
$router->add('GET', '/api/contacts', 'ContactController', 'getAll');

// Edit contacts
$router->add('GET', '/api/contacts/{id}/edit', 'ContactController', 'edit');
$router->add('POST', '/api/contacts/{id}/update', 'ContactController', 'update');
$router->add('POST', '/api/contacts/{id}/delete', 'ContactController', 'delete');

$router->dispatch();