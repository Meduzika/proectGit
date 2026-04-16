<?php
// Файл: src/Models/Review.php
// Модель отзыва (работа с таблицей reviews)

namespace Models;

use PDO;
use PDOException;

class Review {
    // Свойства
    public $id;
    public $text;
    public $rating;
    public $created_at;
    public $user_id;
    public $movie_id;
    
    // Подключение к БД
    private $db;
    
    // Конструктор
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    // Загрузить отзыв по ID
    public function load($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, text, rating, created_at, user_id, movie_id FROM reviews WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                $this->id = $data['id'];
                $this->text = $data['text'];
                $this->rating = $data['rating'];
                $this->created_at = $data['created_at'];
                $this->user_id = $data['user_id'];
                $this->movie_id = $data['movie_id'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки отзыва: " . $e->getMessage());
            return false;
        }
    }
    
    // Получить отзывы к фильму
    public static function getByMovie(PDO $db, $movieId) {
        try {
            $stmt = $db->prepare("
                SELECT r.*, u.login as user_login 
                FROM reviews r 
                INNER JOIN users u ON r.user_id = u.id 
                WHERE r.movie_id = ? 
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$movieId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ошибка получения отзывов: " . $e->getMessage());
            return [];
        }
    }
    
    // Сохранить отзыв (добавить или обновить)
    public function save() {
        try {
            if ($this->id) {
                // Обновление существующего отзыва
                $stmt = $this->db->prepare("
                    UPDATE reviews 
                    SET text = ?, rating = ?, user_id = ?, movie_id = ? 
                    WHERE id = ?
                ");
                return $stmt->execute([
                    $this->text,
                    $this->rating,
                    $this->user_id,
                    $this->movie_id,
                    $this->id
                ]);
            } else {
                // Добавление нового отзыва
                $stmt = $this->db->prepare("
                    INSERT INTO reviews (text, rating, user_id, movie_id, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $result = $stmt->execute([
                    $this->text,
                    $this->rating,
                    $this->user_id,
                    $this->movie_id
                ]);
                
                if ($result) {
                    $this->id = $this->db->lastInsertId();
                }
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Ошибка сохранения отзыва: " . $e->getMessage());
            return false;
        }
    }
    
    // Удалить отзыв
    public function delete() {
        if (!$this->id) return false;
        
        try {
            $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Ошибка удаления отзыва: " . $e->getMessage());
            return false;
        }
    }
}
?>