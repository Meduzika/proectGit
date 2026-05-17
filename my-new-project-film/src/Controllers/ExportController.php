<?php
namespace Controllers;

use Helpers\ExcelHelper;
use Exports\WordExport;

class ExportController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['errors'] = ['Необходимо войти в систему'];
            header('Location: ?page=login');
            exit;
        }
        
        $stmt = $this->pdo->query("SELECT id, title FROM movies ORDER BY title");
        $movies = $stmt->fetchAll();
        
        require __DIR__ . '/../Views/export/index.php';
    }
    
    // ========== EXCEL ОТЧЁТЫ (используем ExcelHelper) ==========
    
    public function excelTop10() {
        $this->checkAuth();
        
        // Получаем TOP-10 фильмов
        $stmt = $this->pdo->query("
            SELECT m.title, m.year, m.director,
                   COALESCE(AVG(r.rating), 0) as avg_rating,
                   COUNT(r.id) as review_count
            FROM movies m
            LEFT JOIN reviews r ON m.id = r.movie_id
            GROUP BY m.id
            ORDER BY avg_rating DESC
            LIMIT 10
        ");
        $movies = $stmt->fetchAll();
        
        $data = [
            'headers' => ['№', 'Название фильма', 'Год', 'Режиссёр', 'Рейтинг', 'Отзывы'],
            'rows' => []
        ];
        
        foreach ($movies as $index => $movie) {
            $data['rows'][] = [
                $index + 1,
                $movie['title'],
                $movie['year'],
                $movie['director'],
                round($movie['avg_rating'], 1) . '/10',
                $movie['review_count']
            ];
        }
        
        ExcelHelper::export($data, 'TOP-10_фильмов_' . date('Y-m-d') . '.xls');
    }
    
    public function excelAllReviews() {
        $this->checkAuth();
        
        $stmt = $this->pdo->query("
            SELECT r.id, r.rating, r.text, r.created_at, 
                   m.title as movie_title, u.login as user_name
            FROM reviews r
            JOIN movies m ON r.movie_id = m.id
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        $reviews = $stmt->fetchAll();
        
        $data = [
            'headers' => ['ID', 'Фильм', 'Пользователь', 'Оценка', 'Отзыв', 'Дата'],
            'rows' => []
        ];
        
        foreach ($reviews as $review) {
            $data['rows'][] = [
                $review['id'],
                $review['movie_title'],
                $review['user_name'],
                $review['rating'] . '/10',
                mb_substr($review['text'], 0, 200),
                date('d.m.Y H:i', strtotime($review['created_at']))
            ];
        }
        
        ExcelHelper::export($data, 'Все_отзывы_' . date('Y-m-d') . '.xls');
    }
    
    public function excelUsersStats() {
        $this->checkAuth();
        
        $stmt = $this->pdo->query("
            SELECT u.login, u.role,
                   COUNT(r.id) as review_count,
                   COALESCE(AVG(r.rating), 0) as avg_rating
            FROM users u
            LEFT JOIN reviews r ON u.id = r.user_id
            GROUP BY u.id, u.login, u.role
            ORDER BY review_count DESC
        ");
        $users = $stmt->fetchAll();
        
        $data = [
            'headers' => ['Пользователь', 'Роль', 'Количество отзывов', 'Средний рейтинг'],
            'rows' => []
        ];
        
        foreach ($users as $user) {
            $data['rows'][] = [
                $user['login'],
                $user['role'] === 'admin' ? 'Администратор' : 'Пользователь',
                $user['review_count'],
                round($user['avg_rating'], 1) . '/10'
            ];
        }
        
        ExcelHelper::export($data, 'Статистика_пользователей_' . date('Y-m-d') . '.xls');
    }
    
    public function excelMovieReviews() {
        $this->checkAuth();
        $movieId = (int)($_GET['movie_id'] ?? 0);
        
        if ($movieId <= 0) {
            header('Location: ?page=export');
            exit;
        }
        
        // Получаем информацию о фильме
        $stmt = $this->pdo->prepare("SELECT title FROM movies WHERE id = ?");
        $stmt->execute([$movieId]);
        $movie = $stmt->fetch();
        
        if (!$movie) {
            header('Location: ?page=export');
            exit;
        }
        
        // Получаем отзывы
        $stmt = $this->pdo->prepare("
            SELECT r.rating, r.text, r.created_at, u.login as user_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.movie_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$movieId]);
        $reviews = $stmt->fetchAll();
        
        $data = [
            'headers' => ['Пользователь', 'Оценка', 'Отзыв', 'Дата'],
            'rows' => []
        ];
        
        foreach ($reviews as $review) {
            $data['rows'][] = [
                $review['user_name'],
                $review['rating'] . '/10',
                $review['text'],
                date('d.m.Y H:i', strtotime($review['created_at']))
            ];
        }
        
        $filename = 'Отзывы_к_фильму_' . $movie['title'] . '_' . date('Y-m-d') . '.xls';
        ExcelHelper::export($data, $filename);
    }
    
    // ========== WORD ОТЧЁТЫ ==========
    
    public function wordTop10() {
        $this->checkAuth();
        $export = new WordExport($this->pdo);
        $export->exportTop10();
    }
    
    public function wordAllReviews() {
        $this->checkAuth();
        $export = new WordExport($this->pdo);
        $export->exportAllReviews();
    }
    
    public function wordUsersStats() {
        $this->checkAuth();
        $export = new WordExport($this->pdo);
        $export->exportUsersStats();
    }
    
    public function wordMovieReviews() {
        $this->checkAuth();
        $movieId = (int)($_GET['movie_id'] ?? 0);
        if ($movieId > 0) {
            $export = new WordExport($this->pdo);
            $export->exportMovieReviews($movieId);
        } else {
            header('Location: ?page=export');
        }
        exit;
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['errors'] = ['Необходимо войти в систему'];
            header('Location: ?page=login');
            exit;
        }
    }
}