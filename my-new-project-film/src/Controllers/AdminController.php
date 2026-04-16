<?php
/**
 * Админ-контроллер
 */

namespace Controllers;

use Models\Movie;
use Models\Review;
use Models\Actor;
use Models\User;

class AdminController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Админ-панель
     */
    public function index() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['errors'] = ['Доступ запрещён!'];
            header('Location: ?page=home');
            exit;
        }
        
        $activeTab = $_GET['tab'] ?? 'movies';
        
        switch ($activeTab) {
            case 'movies':
                $this->showMovies();
                break;
            case 'reviews':
                $this->showReviews();
                break;
            case 'actors':
                $this->showActors();
                break;
            case 'reports':
                $this->showReports();
                break;
            default:
                $this->showMovies();
        }
    }
    
    /**
     * Показать управление фильмами
     */
    private function showMovies() {
        $movies = Movie::getAll($this->pdo);
        $totalMovies = count($movies);
        
        $perPage = 5;
        $totalPages = ceil($totalMovies / $perPage);
        $currentPage = (int)($_GET['p'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $perPage;
        
        $pagedMovies = array_slice($movies, $offset, $perPage);
        $pdo = $this->pdo;
        
        require __DIR__ . '/../Views/admin/index.php';
    }
    
    /**
     * Показать управление отзывами
     */
    private function showReviews() {
        // Получаем все отзывы с информацией о фильме и пользователе
        $stmt = $this->pdo->query("
            SELECT r.*, m.title as movie_title, u.login as user_login 
            FROM reviews r 
            JOIN movies m ON r.movie_id = m.id 
            JOIN users u ON r.user_id = u.id 
            ORDER BY r.created_at DESC
        ");
        $allReviews = $stmt->fetchAll();
        
        $totalReviews = count($allReviews);
        $perPage = 10;
        $totalPages = ceil($totalReviews / $perPage);
        $currentPage = (int)($_GET['p'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $perPage;
        
        $pagedReviews = array_slice($allReviews, $offset, $perPage);
        
        require __DIR__ . '/../Views/admin/reviews.php';
    }
    
    /**
     * Показать управление актёрами
     */
    private function showActors() {
        $stmt = $this->pdo->query("SELECT * FROM actors ORDER BY name");
        $allActors = $stmt->fetchAll();
        
        $totalActors = count($allActors);
        $perPage = 10;
        $totalPages = ceil($totalActors / $perPage);
        $currentPage = (int)($_GET['p'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $perPage;
        
        $pagedActors = array_slice($allActors, $offset, $perPage);
        
        require __DIR__ . '/../Views/admin/actors.php';
    }
    
    /**
     * Показать отчёты
     */
    private function showReports() {
        // Статистика
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM movies");
        $totalMovies = $stmt->fetch()['count'];
        
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM reviews");
        $totalReviews = $stmt->fetch()['count'];
        
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM actors");
        $totalActors = $stmt->fetch()['count'];
        
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM users");
        $totalUsers = $stmt->fetch()['count'];
        
        // TOP-10 фильмов
        $stmt = $this->pdo->query("
            SELECT m.id, m.title, m.year, 
                   COALESCE(AVG(r.rating), 0) as avg_rating,
                   COUNT(r.id) as review_count
            FROM movies m
            LEFT JOIN reviews r ON m.id = r.movie_id
            GROUP BY m.id
            ORDER BY avg_rating DESC
            LIMIT 10
        ");
        $top10Movies = $stmt->fetchAll();
        
        // Топ пользователей по активности
        $stmt = $this->pdo->query("
            SELECT u.login, 
                   COUNT(r.id) as review_count,
                   COALESCE(AVG(r.rating), 0) as avg_rating
            FROM users u
            LEFT JOIN reviews r ON u.id = r.user_id
            GROUP BY u.id
            ORDER BY review_count DESC
            LIMIT 10
        ");
        $topUsers = $stmt->fetchAll();
        
        require __DIR__ . '/../Views/admin/reports.php';
    }
    
    /**
     * API: получить фильмы актёра (для AJAX)
     */
    public function getActorMovies() {
        $actorId = (int)($_GET['actor_id'] ?? 0);
        if ($actorId) {
            $stmt = $this->pdo->prepare("
                SELECT m.title, m.year, ma.role_in_movie
                FROM movies m
                JOIN movie_actor ma ON m.id = ma.movie_id
                WHERE ma.actor_id = ?
            ");
            $stmt->execute([$actorId]);
            echo json_encode($stmt->fetchAll());
        }
        exit;
    }
    
    // ===== CRUD для фильмов =====
    public function create() {
        $this->checkAdmin();
        $title = trim($_POST['title'] ?? '');
        $year = (int)($_POST['year'] ?? 0);
        $director = trim($_POST['director'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (!empty($title) && $year > 0 && !empty($director)) {
            $stmt = $this->pdo->prepare("INSERT INTO movies (title, year, director, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $year, $director, $description]);
            $_SESSION['success'] = '✅ Фильм добавлен!';
        } else {
            $_SESSION['errors'] = ['Заполните все обязательные поля'];
        }
        header('Location: ?page=admin&tab=movies');
        exit;
    }
    
    public function update() {
        $this->checkAdmin();
        $movieId = (int)$_POST['movie_id'];
        $title = trim($_POST['title'] ?? '');
        $year = (int)($_POST['year'] ?? 0);
        $director = trim($_POST['director'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if ($movieId > 0 && !empty($title) && $year > 0 && !empty($director)) {
            $stmt = $this->pdo->prepare("UPDATE movies SET title = ?, year = ?, director = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $year, $director, $description, $movieId]);
            $_SESSION['success'] = '✅ Фильм обновлён!';
        } else {
            $_SESSION['errors'] = ['Заполните все обязательные поля'];
        }
        header('Location: ?page=admin&tab=movies');
        exit;
    }
    
    public function delete() {
        $this->checkAdmin();
        $movieId = (int)$_POST['movie_id'];
        if ($movieId > 0) {
            $stmt = $this->pdo->prepare("DELETE FROM movies WHERE id = ?");
            $stmt->execute([$movieId]);
            $_SESSION['success'] = '✅ Фильм удалён!';
        }
        header('Location: ?page=admin&tab=movies');
        exit;
    }
    
    // ===== CRUD для отзывов =====
    public function updateReview() {
        $this->checkAdmin();
        $reviewId = (int)$_POST['review_id'];
        $rating = (int)$_POST['review_rating'];
        $text = trim($_POST['review_text'] ?? '');
        
        if ($reviewId > 0 && $rating >= 1 && $rating <= 10 && !empty($text)) {
            $stmt = $this->pdo->prepare("UPDATE reviews SET rating = ?, text = ? WHERE id = ?");
            $stmt->execute([$rating, $text, $reviewId]);
            $_SESSION['success'] = '✅ Отзыв обновлён!';
        } else {
            $_SESSION['errors'] = ['Заполните все поля корректно'];
        }
        header('Location: ?page=admin&tab=reviews');
        exit;
    }
    
    public function deleteReview() {
        $this->checkAdmin();
        $reviewId = (int)$_POST['review_id'];
        if ($reviewId > 0) {
            $stmt = $this->pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$reviewId]);
            $_SESSION['success'] = '✅ Отзыв удалён!';
        }
        header('Location: ?page=admin&tab=reviews');
        exit;
    }
    
    // ===== CRUD для актёров =====
    public function saveActor() {
        $this->checkAdmin();
        $actorId = (int)($_POST['actor_id'] ?? 0);
        $name = trim($_POST['actor_name'] ?? '');
        $birthDate = !empty($_POST['actor_birth_date']) ? $_POST['actor_birth_date'] : null;
        
        if (empty($name)) {
            $_SESSION['errors'] = ['Введите имя актёра'];
            header('Location: ?page=admin&tab=actors');
            exit;
        }
        
        if ($actorId > 0) {
            $stmt = $this->pdo->prepare("UPDATE actors SET name = ?, birth_date = ? WHERE id = ?");
            $stmt->execute([$name, $birthDate, $actorId]);
            $_SESSION['success'] = '✅ Актёр обновлён!';
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO actors (name, birth_date) VALUES (?, ?)");
            $stmt->execute([$name, $birthDate]);
            $_SESSION['success'] = '✅ Актёр добавлен!';
        }
        header('Location: ?page=admin&tab=actors');
        exit;
    }
    
    public function deleteActor() {
        $this->checkAdmin();
        $actorId = (int)$_POST['actor_id'];
        if ($actorId > 0) {
            // Сначала удаляем связи с фильмами
            $stmt = $this->pdo->prepare("DELETE FROM movie_actor WHERE actor_id = ?");
            $stmt->execute([$actorId]);
            // Затем удаляем актёра
            $stmt = $this->pdo->prepare("DELETE FROM actors WHERE id = ?");
            $stmt->execute([$actorId]);
            $_SESSION['success'] = '✅ Актёр удалён!';
        }
        header('Location: ?page=admin&tab=actors');
        exit;
    }
    
    private function checkAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['errors'] = ['Доступ запрещён'];
            header('Location: ?page=home');
            exit;
        }
    }
}
?>