<?php
namespace Exports;

class WordExport {
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
        
        $html = $this->getHeader('ТОП-10 фильмов по рейтингу');
        $html .= '<table border="1" cellpadding="8">
            <tr style="background:#e94560; color:white;">
                <th>#</th><th>Название</th><th>Год</th><th>Режиссёр</th><th>Рейтинг</th><th>Отзывы</th>
            </tr>';
        
        foreach ($movies as $index => $m) {
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . htmlspecialchars($m['title']) . '</td>
                <td>' . $m['year'] . '</td>
                <td>' . htmlspecialchars($m['director']) . '</td>
                <td>' . round($m['avg_rating'], 1) . '/10</td>
                <td>' . $m['review_count'] . '</td>
            </tr>';
        }
        
        $html .= '</table>' . $this->getFooter();
        $this->output($html, 'top10_movies.doc');
    }
    
    public function exportAllReviews() {
        $stmt = $this->pdo->query("
            SELECT r.rating, r.text, r.created_at, m.title as movie_title, u.login as user_name
            FROM reviews r
            JOIN movies m ON r.movie_id = m.id
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        $reviews = $stmt->fetchAll();
        
        $html = $this->getHeader('Все отзывы');
        $html .= '<table border="1" cellpadding="8">
            <tr style="background:#e94560; color:white;">
                <th>Фильм</th><th>Пользователь</th><th>Оценка</th><th>Отзыв</th><th>Дата</th>
            </tr>';
        
        foreach ($reviews as $r) {
            $html .= '<tr>
                <td>' . htmlspecialchars($r['movie_title']) . '</td>
                <td>' . htmlspecialchars($r['user_name']) . '</td>
                <td>' . $r['rating'] . '/10</td>
                <td>' . nl2br(htmlspecialchars(mb_substr($r['text'], 0, 200))) . '</td>
                <td>' . date('d.m.Y', strtotime($r['created_at'])) . '</td>
            </tr>';
        }
        
        $html .= '</table>' . $this->getFooter();
        $this->output($html, 'all_reviews.doc');
    }
    
    public function exportUsersStats() {
        $stmt = $this->pdo->query("
            SELECT u.login, u.role, COUNT(r.id) as review_count, COALESCE(AVG(r.rating), 0) as avg_rating
            FROM users u
            LEFT JOIN reviews r ON u.id = r.user_id
            GROUP BY u.id
            ORDER BY review_count DESC
        ");
        $users = $stmt->fetchAll();
        
        $html = $this->getHeader('Статистика пользователей');
        $html .= '<table border="1" cellpadding="8">
            <tr style="background:#e94560; color:white;">
                <th>Пользователь</th><th>Роль</th><th>Отзывов</th><th>Средний рейтинг</th>
            </tr>';
        
        foreach ($users as $u) {
            $html .= '<tr>
                <td>' . htmlspecialchars($u['login']) . '</td>
                <td>' . ($u['role'] === 'admin' ? 'Администратор' : 'Пользователь') . '</td>
                <td>' . $u['review_count'] . '</td>
                <td>' . round($u['avg_rating'], 1) . '/10</td>
            </tr>';
        }
        
        $html .= '</table>' . $this->getFooter();
        $this->output($html, 'users_stats.doc');
    }
    
    public function exportMovieReviews($movieId) {
        $stmt = $this->pdo->prepare("SELECT title FROM movies WHERE id = ?");
        $stmt->execute([$movieId]);
        $movie = $stmt->fetch();
        
        $stmt = $this->pdo->prepare("
            SELECT r.rating, r.text, r.created_at, u.login as user_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.movie_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$movieId]);
        $reviews = $stmt->fetchAll();
        
        $html = $this->getHeader('Отзывы к фильму: ' . htmlspecialchars($movie['title']));
        
        foreach ($reviews as $r) {
            $html .= '<div style="border-left:3px solid #e94560; margin:15px 0; padding:15px;">
                <strong>' . htmlspecialchars($r['user_name']) . '</strong> | 
                Оценка: ' . $r['rating'] . '/10 | 
                Дата: ' . date('d.m.Y', strtotime($r['created_at'])) . '
                <p>' . nl2br(htmlspecialchars($r['text'])) . '</p>
            </div>';
        }
        
        $html .= $this->getFooter();
        $this->output($html, 'movie_reviews.doc');
    }
    
    private function getHeader($title) {
        return '<!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>
        <style>body { font-family: Arial; margin: 40px; } h1 { color: #e94560; }</style>
        </head>
        <body>
        <h1>' . htmlspecialchars($title) . '</h1>
        <p>Дата: ' . date('d.m.Y H:i:s') . '</p><hr>';
    }
    
    private function getFooter() {
        return '<hr><p>Отчёт сгенерирован системой «КиноОтзыв»</p></body></html>';
    }
    
    private function output($html, $filename) {
        header('Content-Type: application/msword');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $html;
        exit;
    }
}