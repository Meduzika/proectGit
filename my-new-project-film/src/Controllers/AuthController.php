<?php
/**
 * Контроллер авторизации
 * Файл: src/Controllers/AuthController.php
 */

namespace Controllers;

use Models\User;

class AuthController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Страница входа
     */
    public function login() {
        // Если уже авторизован — редирект
        if (isset($_SESSION['user_id'])) {
            header('Location: ?page=home');
            exit;
        }
        
        // ✅ ПРАВИЛЬНЫЙ ПУТЬ (без папки auth!)
        require dirname(__DIR__) . '/Views/login.php';
    }
    
    /**
     * Обработка входа
     */
    public function loginSubmit() {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($login) || empty($password)) {
            $_SESSION['login_error'] = 'Введите логин и пароль';
            header('Location: ?page=login');
            exit;
        }
        
        $user = User::authenticate($this->pdo, $login, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header('Location: ?page=admin');
            } else {
                header('Location: ?page=home');
            }
            exit;
        } else {
            $_SESSION['login_error'] = 'Неверный логин или пароль';
            header('Location: ?page=login');
            exit;
        }
    }
    
    /**
     * Выход
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ?page=home');
        exit;
    }
}