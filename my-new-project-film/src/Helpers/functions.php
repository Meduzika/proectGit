<?php
/**
 * Простая защита от XSS
 * Использование: вместо echo $var пиши echo e($var)
 */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}
?>