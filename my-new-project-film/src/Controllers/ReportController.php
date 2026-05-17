<?php
/**
 * Контроллер отчётов
 * Файл: src/Controllers/ReportController.php
 */

namespace Controllers;

use Models\Movie;
use Models\Actor;
use Models\Review;
use Helpers\ExcelHelper;

class ReportController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Страница отчётов
     */
    public function index() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['errors'] = ['Доступ запрещён!'];
            header('Location: ?page=home');
            exit;
        }
        
        require __DIR__ . '/../Views/admin/reports.php';
    }
    
    /**
     * TOP-10 отчёт
     */
    public function top10() {
        $movies = Movie::getAll($this->pdo);
        
        $moviesWithStats = [];
        foreach ($movies as $movie) {
            $reviews = Review::getByMovie($this->pdo, $movie->id);
            $avgRating = 0;
            if (!empty($reviews)) {
                $total = array_sum(array_column($reviews, 'rating'));
                $avgRating = round($total / count($reviews), 2);
            }
            $moviesWithStats[] = [
                'title' => $movie->title,
                'year' => $movie->year,
                'rating' => $avgRating,
                'reviews' => count($reviews)
            ];
        }
        
        usort($moviesWithStats, function($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });
        
        return array_slice($moviesWithStats, 0, 10);
    }
    /**
 * Получить ВСЕ отзывы (для отчёта)
 */
public function getAllReviews() {
    $stmt = $this->pdo->query("
        SELECT r.*, m.title as movie_title, u.login as user_login
        FROM reviews r
        JOIN movies m ON r.movie_id = m.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

/**
 * Получить статистику по ВСЕМ пользователям
 */
public function getUserStats() {
    $stmt = $this->pdo->query("
        SELECT u.id, u.login, u.role,
               COUNT(r.id) as total_reviews,
               COALESCE(AVG(r.rating), 0) as avg_rating
        FROM users u
        LEFT JOIN reviews r ON u.id = r.user_id
        GROUP BY u.id, u.login, u.role
        ORDER BY total_reviews DESC
    ");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    
    /**
     * Отчёт по актёрам
     */
    public function actors() {
        return Actor::getAllWithStats($this->pdo);
    }
    
    /**
     * Отчёт по активности пользователя
     */
    public function userActivity($userId = null, $startDate = null, $endDate = null) {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? 0;
        }
        
        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');
        }
        
        $stmt = $this->pdo->prepare("
            SELECT 
                r.id,
                r.text,
                r.rating,
                r.created_at,
                m.title as movie_title
            FROM reviews r
            INNER JOIN movies m ON r.movie_id = m.id
            WHERE r.user_id = ?
            AND r.created_at BETWEEN ? AND ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$userId, $startDate, $endDate]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Статистика пользователя
     */
    public function userStats($userId = null) {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? 0;
        }
        
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as avg_rating,
                MIN(created_at) as first_review,
                MAX(created_at) as last_review
            FROM reviews
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    /**
 * Получить все отзывы для отображения на странице
 */
public function getAllReviewsForDisplay() {
    $stmt = $this->pdo->query("
        SELECT r.*, m.title as movie_title, u.login as user_login
        FROM reviews r
        JOIN movies m ON r.movie_id = m.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

/**
 * Получить статистику пользователей для отображения
 */
public function getUserStatsForDisplay() {
    $stmt = $this->pdo->query("
        SELECT u.id, u.login, u.role,
               COUNT(r.id) as total_reviews,
               COALESCE(AVG(r.rating), 0) as avg_rating,
               MIN(r.created_at) as first_review,
               MAX(r.created_at) as last_review
        FROM users u
        LEFT JOIN reviews r ON u.id = r.user_id
        GROUP BY u.id, u.login, u.role
        ORDER BY total_reviews DESC
    ");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

/**
 * Получить отзывы по фильму для отображения
 */
public function getMovieReviewsForDisplay($movieId) {
    $stmt = $this->pdo->prepare("
        SELECT r.*, u.login as user_login, m.title as movie_title
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN movies m ON r.movie_id = m.id
        WHERE r.movie_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$movieId]);
    $reviews = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Получаем информацию о фильме
    $stmt = $this->pdo->prepare("SELECT title FROM movies WHERE id = ?");
    $stmt->execute([$movieId]);
    $movie = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    return [
        'movie' => $movie,
        'reviews' => $reviews
    ];
}
    /**
 * Экспорт в Excel
 */
public function exportExcel($type) {
    $this->checkAdmin();
    
    switch ($type) {
        case 'top10':
            $data = [
                'headers' => ['№', 'Название фильма', 'Год', 'Режиссёр', 'Рейтинг', 'Отзывы'],
                'rows' => []
            ];
            
            $top10 = $this->top10();
            foreach ($top10 as $index => $movie) {
                $data['rows'][] = [
                    $index + 1,
                    $movie['title'],
                    $movie['year'],
                    $movie['director'],
                    $movie['rating'],
                    $movie['reviews']
                ];
            }
            
            \Helpers\ExcelHelper::export($data, 'TOP-10_фильмов_' . date('Y-m-d') . '.xls');
            break;
            
        case 'actors':
            $data = [
                'headers' => ['Актёр', 'Дата рождения', 'Фильмов', 'Ср. рейтинг', 'Отзывов'],
                'rows' => []
            ];
            
            $actors = $this->actors();
            foreach ($actors as $actor) {
                $data['rows'][] = [
                    $actor['name'],
                    $actor['birth_date'] ?? '',
                    $actor['films_count'],
                    $actor['avg_rating'] ?? 0,
                    $actor['reviews_count']
                ];
            }
            
            \Helpers\ExcelHelper::export($data, 'Актёры_статистика_' . date('Y-m-d') . '.xls');
            break;
            
        case 'user_activity':
            $userId = $_GET['user_id'] ?? $_SESSION['user_id'];
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-d');
            
            $data = [
                'headers' => ['Дата', 'Фильм', 'Оценка', 'Текст отзыва'],
                'rows' => []
            ];
            
            $reviews = $this->userActivity($userId, $startDate, $endDate);
            foreach ($reviews as $review) {
                $data['rows'][] = [
                    $review['created_at'],
                    $review['movie_title'],
                    $review['rating'],
                    $review['text']
                ];
            }
            
            \Helpers\ExcelHelper::export($data, 'Активность_' . date('Y-m-d') . '.xls');
            break;
    }
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