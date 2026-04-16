<?php
/**
 * Front Controller - единая точка входа
 */

session_start();

// ===== АВТОЗАГРУЗКА КЛАССОВ =====
spl_autoload_register(function ($class) {
    $prefixes = [
        'Controllers\\' => __DIR__ . '/../src/Controllers/',
        'Models\\' => __DIR__ . '/../src/Models/',
        'Exports\\' => __DIR__ . '/../src/Exports/',
    ];
    
    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

// ===== ПОДКЛЮЧЕНИЕ ФУНКЦИЙ =====
require_once __DIR__ . '/../src/Helpers/functions.php';

// ===== CSRF ТОКЕН =====
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ===== ПОДКЛЮЧЕНИЕ БД =====
require_once __DIR__ . '/../src/Settings/pdo.php';

// ===== ПОЛУЧАЕМ ПОДКЛЮЧЕНИЕ К БД =====
$conn = getConnection();
if (!$conn[0]) {
    die("<h1 style='color:red'>❌ Ошибка БД: " . $conn[1] . "</h1>");
}
$pdo = $conn[1];

// ===== СОЗДАЁМ КОНТРОЛЛЕРЫ =====
$movieController = new Controllers\MovieController($pdo);
$authController = new Controllers\AuthController($pdo);
$reviewController = new Controllers\ReviewController($pdo);
$adminController = new Controllers\AdminController($pdo);
$exportController = new Controllers\ExportController($pdo);

// ===== ОБРАБОТКА ЭКСПОРТА (ДО РОУТИНГА) =====
if (isset($_GET['page']) && $_GET['page'] === 'export' && isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'excel_top10': $exportController->excelTop10(); exit;
        case 'excel_reviews': $exportController->excelAllReviews(); exit;
        case 'excel_users': $exportController->excelUsersStats(); exit;
        case 'word_top10': $exportController->wordTop10(); exit;
        case 'word_movie': $exportController->wordMovieReviews(); exit;
    }
}

// ===== ОБРАБОТКА ВЫХОДА =====
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ?page=home');
    exit;
}

// ===== ОБРАБОТКА POST-ЗАПРОСОВ =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Проверка CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('<h1 style="color:red">❌ Ошибка безопасности (CSRF)</h1>');
    }
    
    // 1. ВХОД
    if (isset($_POST['login_submit'])) {
        $authController->loginSubmit();
        exit;
    }
    
    // 2. ДОБАВЛЕНИЕ ОТЗЫВА
    if (isset($_POST['submit_review']) && isset($_GET['id'])) {
        $reviewController->store((int)$_GET['id']);
        exit;
    }
    
    // 3. АДМИН: ДОБАВЛЕНИЕ ФИЛЬМА
    if (isset($_POST['add_movie'])) {
        $adminController->create();
        exit;
    }
    
    // 4. АДМИН: РЕДАКТИРОВАНИЕ ФИЛЬМА
    if (isset($_POST['edit_movie'])) {
        $adminController->update();
        exit;
    }
    
    // 5. АДМИН: УДАЛЕНИЕ ФИЛЬМА
    if (isset($_POST['delete_movie'])) {
        $adminController->delete();
        exit;
    }
    
    // 6. АДМИН: РЕДАКТИРОВАНИЕ ОТЗЫВА
    if (isset($_POST['edit_review'])) {
        $adminController->updateReview();
        exit;
    }
    
    // 7. АДМИН: УДАЛЕНИЕ ОТЗЫВА
    if (isset($_POST['delete_review'])) {
        $adminController->deleteReview();
        exit;
    }
    
    // 8. АДМИН: СОХРАНЕНИЕ АКТЁРА (добавить/редактировать)
    if (isset($_POST['save_actor'])) {
        $adminController->saveActor();
        exit;
    }
    
    // 9. АДМИН: УДАЛЕНИЕ АКТЁРА
    if (isset($_POST['delete_actor'])) {
        $adminController->deleteActor();
        exit;
    }
}

// ===== ОБРАБОТКА AJAX =====
if (isset($_GET['action']) && $_GET['action'] === 'get_movies') {
    $adminController->getActorMovies();
    exit;
}

// ===== РОУТИНГ =====
$page = $_GET['page'] ?? 'home';
$id = (int)($_GET['id'] ?? 0);
$currentPage = $page;

// Делаем $pdo доступным для всех view
$GLOBALS['pdo'] = $pdo;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>КиноОтзыв</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <a href="?page=home" class="logo">🎬 КиноОтзыв</a>
            <nav class="nav">
                <a href="?page=home" class="<?= $currentPage === 'home' ? 'active' : '' ?>">🏠 Главная</a>
                <a href="?page=movies" class="<?= $currentPage === 'movies' ? 'active' : '' ?>">🎥 Каталог</a>
                <a href="?page=top10" class="<?= $currentPage === 'top10' ? 'active' : '' ?>">🏆 TOP-10</a>
                <a href="?page=about" class="<?= $currentPage === 'about' ? 'active' : '' ?>">ℹ️ О проекте</a>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="?page=admin" class="<?= $currentPage === 'admin' ? 'active' : '' ?>" style="background: #e94560; color: white;">⚙️ Админка</a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="?page=export" class="<?= $currentPage === 'export' ? 'active' : '' ?>">📄 Экспорт</a>
                    <span class="user-info">👤 <?= e($_SESSION['user_login']) ?></span>
                    <a href="?logout=1" style="background: #dc3545; border-color: #dc3545;">🚪 Выход</a>
                <?php else: ?>
                    <a href="?page=login" style="background: #28a745; border-color: #28a745;">🔐 Вход</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= e($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-error"><?= e($_SESSION['login_error']) ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>
        
        <?php
        // ===== ВЫЗОВ КОНТРОЛЛЕРОВ =====
        switch ($page) {
            case 'home':
                $movieController->index();
                break;
            case 'movies':
                $movieController->catalog();
                break;
            case 'movie':
                if ($id > 0) {
                    $movieController->show($id);
                } else {
                    echo '<h1 class="alert alert-error">❌ Фильм не найден</h1>';
                }
                break;
            case 'top10':
                $movieController->top10();
                break;
            case 'about':
                require __DIR__ . '/../src/Views/about.php';
                break;
            case 'login':
                $authController->login();
                break;
            case 'admin':
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    $adminController->index();
                } else {
                    echo '<div class="alert alert-error">❌ Доступ запрещён! Только для администраторов.</div>';
                }
                break;
            case 'export':
                $exportController->index();
                break;
            default:
                echo '<h1 class="alert alert-error">❌ Страница не найдена</h1>';
        }
        ?>
    </div>
</body>
</html>