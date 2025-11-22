<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $this->view('home', ['login' => $_SESSION['user_login']]);
    }
}
