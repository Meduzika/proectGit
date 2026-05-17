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
                <a href="?page=home" class="<?= $page === 'home' ? 'active' : '' ?>">🏠 Главная</a>
                <a href="?page=movies" class="<?= $page === 'movies' ? 'active' : '' ?>">🎥 Каталог</a>
                <a href="?page=top10" class="<?= $page === 'top10' ? 'active' : '' ?>">🏆 TOP-10</a>
                <a href="?page=about" class="<?= $page === 'about' ? 'active' : '' ?>">ℹ️ О проекте</a>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="?page=admin" class="<?= $page === 'admin' ? 'active' : '' ?>" style="background: #e94560; color: white;">⚙️ Админка</a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="user-info">👤 <?= e($_SESSION['user_login']) ?></span>
                    <a href="?logout=1" style="background: #dc3545; border-color: #dc3545;">🚪 Выход</a>
                <?php else: ?>
                    <a href="?page=login" style="background: #28a745; border-color: #28a745;">🔐 Вход</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    
    <div class="container">