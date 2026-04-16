<?php
namespace Exports;

class ExcelExport {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function exportTop10() {
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
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="top10_movies_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['#', 'Название', 'Год', 'Режиссёр', 'Рейтинг', 'Отзывы']);
        
        foreach ($movies as $index => $movie) {
            fputcsv($output, [
                $index + 1,
                $movie['title'],
                $movie['year'],
                $movie['director'],
                round($movie['avg_rating'], 1) . '/10',
                $movie['review_count']
            ]);
        }
        fclose($output);
        exit;
    }
    
    public function exportAllReviews() {
        $stmt = $this->pdo->query("
            SELECT m.title as movie_title, u.login as user_name, 
                   r.rating, r.text, r.created_at
            FROM reviews r
            JOIN movies m ON r.movie_id = m.id
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        $reviews = $stmt->fetchAll();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="all_reviews_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Фильм', 'Пользователь', 'Оценка', 'Отзыв', 'Дата']);
        
        foreach ($reviews as $review) {
            fputcsv($output, [
                $review['movie_title'],
                $review['user_name'],
                $review['rating'] . '/10',
                $review['text'],
                $review['created_at']
            ]);
        }
        fclose($output);
        exit;
    }
    
    public function exportUsersStats() {
        $stmt = $this->pdo->query("
            SELECT u.login, COUNT(r.id) as review_count,
                   COALESCE(AVG(r.rating), 0) as avg_rating
            FROM users u
            LEFT JOIN reviews r ON u.id = r.user_id
            GROUP BY u.id
            ORDER BY review_count DESC
        ");
        $users = $stmt->fetchAll();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="users_stats_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Пользователь', 'Отзывов', 'Средний рейтинг']);
        
        foreach ($users as $user) {
            fputcsv($output, [
                $user['login'],
                $user['review_count'],
                round($user['avg_rating'], 1) . '/10'
            ]);
        }
        fclose($output);
        exit;
    }
}