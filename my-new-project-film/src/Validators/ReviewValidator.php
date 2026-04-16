<?php
/**
 * Валидатор отзывов
 * Файл: src/Validators/ReviewValidator.php
 */

namespace Validators;

class ReviewValidator {
    
    /**
     * Валидация данных отзыва
     * 
     * @param array $data Данные из формы
     * @return array ['success' => bool, 'errors' => array, 'data' => array]
     */
    public static function validate(array $data): array {
        $errors = [];
        $cleanData = [];
        
        // ===== 1. ВАЛИДАЦИЯ ТЕКСТА ОТЗЫВА =====
        $text = trim($data['text'] ?? '');
        
        if (empty($text)) {
            $errors[] = 'Введите текст отзыва';
        } else {
            // Удаляем все HTML-теги (защита от XSS)
            $text = strip_tags($text);
            
            // Проверяем минимальную длину
            if (strlen($text) < 10) {
                $errors[] = 'Отзыв должен содержать минимум 10 символов (сейчас: ' . strlen($text) . ')';
            }
            
            // Проверяем максимальную длину
            if (strlen($text) > 1000) {
                $errors[] = 'Отзыв не должен превышать 1000 символов (сейчас: ' . strlen($text) . ')';
            }
            
            // Проверяем на опасные символы
            if (preg_match('/[<>]/', $text)) {
                $errors[] = 'Отзыв содержит недопустимые символы (< или >)';
            }
            
            // Сохраняем очищенный текст
            $cleanData['text'] = $text;
        }
        
        // ===== 2. ВАЛИДАЦИЯ ОЦЕНКИ =====
        $rating = (int)($data['rating'] ?? 0);
        
        if ($rating < 1 || $rating > 10) {
            $errors[] = 'Оценка должна быть от 1 до 10 (сейчас: ' . $rating . ')';
        }
        
        $cleanData['rating'] = $rating;
        
        // ===== 3. ВАЛИДАЦИЯ ID ФИЛЬМА =====
        $movieId = (int)($data['movie_id'] ?? 0);
        
        if ($movieId <= 0) {
            $errors[] = 'Неверный ID фильма';
        }
        
        $cleanData['movie_id'] = $movieId;
        
        // ===== 4. ВАЛИДАЦИЯ ID ПОЛЬЗОВАТЕЛЯ =====
        $userId = (int)($data['user_id'] ?? 0);
        
        if ($userId <= 0) {
            $errors[] = 'Пользователь не авторизован';
        }
        
        $cleanData['user_id'] = $userId;
        
        // ===== ВОЗВРАЩАЕМ РЕЗУЛЬТАТ =====
        return [
            'success' => empty($errors),
            'errors' => $errors,
            'data' => $cleanData
        ];
    }
    
    /**
     * Быстрая проверка без возврата данных
     */
    public static function isValid(array $data): bool {
        $result = self::validate($data);
        return $result['success'];
    }
}
?>