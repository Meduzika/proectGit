<?php
/**
 * Модель пользователя
 * Файл: src/Models/User.php
 */

namespace Models;

use PDO;
use PDOException;

class User {
    public $id;
    public $login;
    public $password;
    public $role;
    
    private $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    /**
     * Загрузить пользователя по ID
     */
    public function load($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, login, password, role FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                $this->id = $data['id'];
                $this->login = $data['login'];
                $this->password = $data['password'];
                $this->role = $data['role'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки пользователя: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Найти пользователя по логину
     */
    public static function getByLogin(PDO $db, $login) {
        try {
            $stmt = $db->prepare("SELECT id, login, password, role FROM users WHERE login = ?");
            $stmt->execute([$login]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                $user = new self($db);
                $user->id = $data['id'];
                $user->login = $data['login'];
                $user->password = $data['password'];
                $user->role = $data['role'];
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            error_log("Ошибка поиска по логину: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Получить всех пользователей
     */
    public static function getAll(PDO $db) {
        try {
            $stmt = $db->query("SELECT id, login, role FROM users ORDER BY id");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $users = [];
            foreach ($rows as $row) {
                $user = new self($db);
                $user->id = $row['id'];
                $user->login = $row['login'];
                $user->role = $row['role'];
                $users[] = $user;
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Ошибка получения списка пользователей: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Проверка логина и пароля
     */
    public static function authenticate(PDO $pdo, string $login, string $password) {
        $stmt = $pdo->prepare("SELECT id, login, password, role FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    /**
     * Проверка, является ли текущий пользователь админом
     */
    public static function isAdmin(): bool {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Получить текущего пользователя
     */
    public static function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'login' => $_SESSION['user_login'] ?? '',
                'role' => $_SESSION['user_role'] ?? ''
            ];
        }
        return null;
    }
}
?>