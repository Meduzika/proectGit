<?php
namespace Exports;

class WordExport {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function exportTop10() {
        $stmt = $this->pdo->query("
            SELECT m.title, m.year, m.director, m.description,
                   COALESCE(AVG(r.rating), 0) as avg_rating,
                   COUNT(r.id) as review_count
            FROM movies m
            LEFT JOIN reviews r ON m.id = r.movie_id
            GROUP BY m.id
            ORDER BY avg_rating DESC
            LIMIT 10
        ");
        $movies = $stmt->fetchAll();
        
        $html = '<!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>ТОП-10 фильмов</title>
        <style>
            body { font-family: Arial; margin: 40px; }
            h1 { color: #e94560; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background: #e94560; color: white; }
        </style>
        </head>
        <body>
            <h1>🏆 ТОП-10 фильмов по рейтингу</h1>
            <p>Дата: ' . date('d.m.Y H:i:s') . '</p>
            <table>
                <thead><tr><th>#</th><th>Название</th><th>Год</th><th>Режиссёр</th><th>Рейтинг</th><th>Отзывы</th></tr></thead>
                <tbody>';
        
        foreach ($movies as $index => $movie) {
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . htmlspecialchars($movie['title']) . '</td>
                <td>' . $movie['year'] . '</td>
                <td>' . htmlspecialchars($movie['director']) . '</td>
                <td>⭐ ' . round($movie['avg_rating'], 1) . '/10</td>
                <td>' . $movie['review_count'] . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table></body></html>';
        
        header('Content-Type: application/msword');
        header('Content-Disposition: attachment; filename="top10_movies_' . date('Y-m-d') . '.doc"');
        echo $html;
        exit;
    }
    
    public function exportMovieReviews($movieId) {
        $stmt = $this->pdo->prepare("
            SELECT m.title as movie_title, r.rating, r.text, r.created_at, u.login as user_name
            FROM reviews r
            JOIN movies m ON r.movie_id = m.id
            JOIN users u ON r.user_id = u.id
            WHERE r.movie_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$movieId]);
        $reviews = $stmt->fetchAll();
        
        $stmt = $this->pdo->prepare("SELECT title FROM movies WHERE id = ?");
        $stmt->execute([$movieId]);
        $movie = $stmt->fetch();
        
        $html = '<!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>Отзывы к фильму</title>
        <style>
            body { font-family: Arial; margin: 40px; }
            h1 { color: #e94560; }
            .review { border-left: 4px solid #e94560; margin: 15px 0; padding: 10px; background: #f9f9f9; }
        </style>
        </head>
        <body>
            <h1>📝 Отзывы к фильму: ' . htmlspecialchars($movie['title']) . '</h1>
            <p>Дата: ' . date('d.m.Y H:i:s') . '</p>
            <p>Всего отзывов: ' . count($reviews) . '</p>';
        
        foreach ($reviews as $review) {
            $html .= '<div class="review">
                <strong>👤 ' . htmlspecialchars($review['user_name']) . '</strong><br>
                ⭐ Оценка: ' . $review['rating'] . '/10<br>
                📅 ' . $review['created_at'] . '<br>
                <p>' . nl2br(htmlspecialchars($review['text'])) . '</p>
            </div>';
        }
        
        $html .= '</body></html>';
        
        header('Content-Type: application/msword');
        header('Content-Disposition: attachment; filename="reviews_movie_' . $movieId . '_' . date('Y-m-d') . '.doc"');
        echo $html;
        exit;
    }
}