<?php
// Файл: src/Models/Actor.php
// Модель актёра (работа с таблицей actors)

namespace Models;

use PDO;
use PDOException; 

class Actor {
    // Свойства
    public $id;
    public $name; 
    public $birth_date;  
    
    // Подключение к БД 
    private $db;
    
    // Конструктор
    public function __construct(PDO $db) {
        $this->db = $db; 
    }
    
    // Загрузить актёра по ID 
    public function load($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, birth_date FROM actors WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC); 
            
            if ($data) {
                $this->id = $data['id'];
                $this->name = $data['name'];
                $this->birth_date = $data['birth_date']; 
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки актёра: " . $e->getMessage());
            return false;
        }
    }
    
    // Получить всех актёров
    public static function getAll(PDO $db) {
        try {
            $stmt = $db->query("SELECT id, name, birth_date FROM actors ORDER BY name");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $actors = [];
            foreach ($rows as $row) {
                $actor = new self($db);
                $actor->id = $row['id'];
                $actor->name = $row['name'];
                $actor->birth_date = $row['birth_date'];
                $actors[] = $actor;
            }
            return $actors;
        } catch (PDOException $e) {
            error_log("Ошибка получения списка актёров: " . $e->getMessage());
            return [];
        }
    }
    
    // Получить актёров фильма
    public static function getByMovie(PDO $db, $movieId) {
        try {
            $stmt = $db->prepare("
                SELECT a.*, ma.role_name 
                FROM actors a 
                INNER JOIN movie_actor ma ON a.id = ma.actor_id 
                WHERE ma.movie_id = ? 
                ORDER BY a.name
            ");
            $stmt->execute([$movieId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ошибка получения актёров фильма: " . $e->getMessage());
            return [];
        }
    }
}
?>