<?php
/**
 * Контроллер отзывов
 * Файл: src/Controllers/ReviewController.php
 */

namespace Controllers;

use Models\Review;

class ReviewController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Добавление отзыва
     */
    public function store($movieId) {
        // Только для авторизованных
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['errors'] = ['Необходимо войти в систему'];
            header('Location: ?page=movie&id=' . $movieId);
            exit;
        }
        
        require_once __DIR__ . '/../Validators/ReviewValidator.php';
        
        $validation = \Validators\ReviewValidator::validate([
            'text' => $_POST['review_text'] ?? '',
            'rating' => (int)($_POST['review_rating'] ?? 0),
            'movie_id' => $movieId,
            'user_id' => $_SESSION['user_id']
        ]);
        
        if ($validation['success']) {
            $review = new Review($this->pdo);
            $review->text = $validation['data']['text'];
            $review->rating = $validation['data']['rating'];
            $review->user_id = $validation['data']['user_id'];
            $review->movie_id = $validation['data']['movie_id'];
            
            if ($review->save()) {
                $_SESSION['success'] = '✅ Отзыв добавлен!';
            } else {
                $_SESSION['errors'] = ['Ошибка при сохранении отзыва'];
            }
        } else {
            $_SESSION['errors'] = $validation['errors'];
        }
        
        header('Location: ?page=movie&id=' . $movieId);
        exit;
    }
}
?>