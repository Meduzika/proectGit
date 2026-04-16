<?php
// Файл: src/Settings/pdo.php
// Этот файл создаёт подключение к базе данных

function getConnection() {
    // Параметры подключения
    $host = 'localhost'; // Хост базы данных
    $dbname = 'film_database'; // Имя базы данных
    $username = 'root'; // По умолчанию в XAMPP пользователь root
    $password = ''; // По умолчанию в XAMPP пароль пустой
    $charset = 'utf8mb4'; // Кодировка  
    
    // DSN (Data Source Name) — строка подключения
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset"; 
    
    // Настройки PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Показывать ошибки
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Возвращать ассоциативные массивы
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Защита от SQL-инъекций
    ];
    
    try {
        // Создаём подключение
        $pdo = new PDO($dsn, $username, $password, $options);  
        return [true, $pdo]; // Успех: возвращаем true и объект PDO
    } catch (PDOException $e) { 
        // Ошибка: возвращаем false и сообщение
        return [false, 'Ошибка подключения: ' . $e->getMessage()]; 
    }
}
?>
