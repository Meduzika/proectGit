<?php
/**
 * Контроллер фильмов
 * Файл: src/Controllers/MovieController.php
 */

namespace Controllers;

use Models\Movie;
use Models\Review;

class MovieController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Главная страница
     */
    public function index() {
        $movies = Movie::getAll($this->pdo);
        $topMovies = array_slice($movies, 0, 6);
        
        require dirname(__DIR__) . '/Views/home.php';
    }
    
    /**
     * Каталог фильмов
     */
    public function catalog() {
        $movies = Movie::getAll($this->pdo);
        
        require dirname(__DIR__) . '/Views/movies.php';
    }
    
    /**
     * Страница фильма
     */
    public function show($id) {
        $movie = Movie::getByIdWithRelations($this->pdo, $id);
        
        if (!$movie) {
            $_SESSION['errors'] = ['Фильм не найден'];
            header('Location: ?page=movies');
            exit;
        }
        
        $reviews = $movie->reviews ?? [];
        
        require dirname(__DIR__) . '/Views/movie.php';
    }
    
    /**
     * TOP-10 фильмов
     */
    public function top10() {
        $movies = Movie::getAll($this->pdo);
        
        $moviesWithStats = [];
        foreach ($movies as $movie) {
            $reviews = Review::getByMovie($this->pdo, $movie->id);
            $avgRating = 0;
            if (!empty($reviews)) {
                $total = array_sum(array_column($reviews, 'rating'));
                $avgRating = round($total / count($reviews), 1);
            }
            $moviesWithStats[] = [
                'movie' => $movie,
                'rating' => $avgRating,
                'review_count' => count($reviews)
            ];
        }
        
        usort($moviesWithStats, function($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });
        
        $top10 = array_slice($moviesWithStats, 0, 10);
        
        require dirname(__DIR__) . '/Views/top10.php';
    }
}
?>