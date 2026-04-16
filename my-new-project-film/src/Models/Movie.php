<?php
/**
 * Модель фильма
 * Файл: src/Models/Movie.php
 */

namespace Models;

use PDO;
use PDOException;

class Movie {
    public $id;
    public $title;
    public $year;
    public $director;
    public $description;
    public $actors;
    public $reviews;

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function load($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, title, year, director, description FROM movies WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                $this->id = $data['id'];
                $this->title = $data['title'];
                $this->year = $data['year'];
                $this->director = $data['director'];
                $this->description = $data['description'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки фильма: " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(PDO $pdo) {
        try {
            $stmt = $pdo->query("SELECT id, title, year, director, description FROM movies ORDER BY year DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $movies = [];
            foreach ($rows as $row) {
                $movie = new self($pdo);
                $movie->id = $row['id'];
                $movie->title = $row['title'];
                $movie->year = $row['year'];
                $movie->director = $row['director'];
                $movie->description = $row['description'];
                $movies[] = $movie;
            }
            return $movies;
        } catch (PDOException $e) {
            error_log("Ошибка получения списка фильмов: " . $e->getMessage());
            return [];
        }
    }

    public static function getByIdWithRelations(PDO $pdo, int $id) {
        $movie = new self($pdo);
        if (!$movie->load($id)) {
            return null;
        }
        
        // ИСПРАВЛЕНО: role_in_movie вместо role_name
        $stmt = $pdo->prepare("
            SELECT a.*, ma.role_in_movie 
            FROM actors a 
            INNER JOIN movie_actor ma ON a.id = ma.actor_id 
            WHERE ma.movie_id = ?
        ");
        $stmt->execute([$id]);
        $movie->actors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("
            SELECT r.*, u.login as user_login 
            FROM reviews r 
            INNER JOIN users u ON r.user_id = u.id 
            WHERE r.movie_id = ? 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$id]);
        $movie->reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $movie;
    }

    public static function searchByTitle(PDO $pdo, string $query) {
        try {
            $stmt = $pdo->prepare("SELECT id, title, year, director, description FROM movies WHERE title LIKE ?");
            $stmt->execute(["%$query%"]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $movies = [];
            foreach ($rows as $row) {
                $movie = new self($pdo);
                $movie->id = $row['id'];
                $movie->title = $row['title'];
                $movie->year = $row['year'];
                $movie->director = $row['director'];
                $movie->description = $row['description'];
                $movies[] = $movie;
            }
            return $movies;
        } catch (PDOException $e) {
            error_log("Ошибка поиска фильмов: " . $e->getMessage());
            return [];
        }
    }

    public function save() {
        try {
            if ($this->id) {
                $stmt = $this->db->prepare("
                    UPDATE movies 
                    SET title = ?, year = ?, director = ?, description = ? 
                    WHERE id = ?
                ");
                return $stmt->execute([
                    $this->title,
                    $this->year,
                    $this->director,
                    $this->description,
                    $this->id
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO movies (title, year, director, description) 
                    VALUES (?, ?, ?, ?)
                ");
                $result = $stmt->execute([
                    $this->title,
                    $this->year,
                    $this->director,
                    $this->description
                ]);
                if ($result) {
                    $this->id = $this->db->lastInsertId();
                }
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Ошибка сохранения фильма: " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        if (!$this->id) return false;
        
        try {
            $stmt = $this->db->prepare("DELETE FROM movies WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Ошибка удаления фильма: " . $e->getMessage());
            return false;
        }
    }
}
?>