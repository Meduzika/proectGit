<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo '<div class="alert alert-error">❌ Доступ запрещён!</div>';
    exit;
}
?>

<div class="reports-main">
    <h1>📊 Отчёты</h1>
    
    <div class="reports-grid">
        <!-- TOP-10 фильмов -->
        <div class="report-card">
            <div class="report-icon">🏆</div>
            <h3>TOP-10 фильмов</h3>
            <p>Лучшие фильмы по рейтингу пользователей</p>
            <div class="report-buttons">
                <a href="?page=report_top10" class="btn-view">👁️ Просмотр</a>
                <a href="?page=export_excel&action=top10" class="btn-excel">📊 Excel</a>
                <a href="?page=export_word&action=top10" class="btn-word">📝 Word</a>
            </div>
        </div>
        
        <!-- Все отзывы -->
        <div class="report-card">
            <div class="report-icon">💬</div>
            <h3>Все отзывы</h3>
            <p>Полный список всех отзывов пользователей</p>
            <div class="report-buttons">
                <a href="?page=report_all_reviews" class="btn-view">👁️ Просмотр</a>
                <a href="?page=export_excel&action=all_reviews" class="btn-excel">📊 Excel</a>
                <a href="?page=export_word&action=all_reviews" class="btn-word">📝 Word</a>
            </div>
        </div>
        
        <!-- Статистика пользователей -->
        <div class="report-card">
            <div class="report-icon">👥</div>
            <h3>Статистика пользователей</h3>
            <p>Активность и рейтинги пользователей</p>
            <div class="report-buttons">
                <a href="?page=report_user_stats" class="btn-view">👁️ Просмотр</a>
                <a href="?page=export_excel&action=user_stats" class="btn-excel">📊 Excel</a>
                <a href="?page=export_word&action=user_stats" class="btn-word">📝 Word</a>
            </div>
        </div>
        
        <!-- Актёры со статистикой -->
        <div class="report-card">
            <div class="report-icon">🎭</div>
            <h3>Статистика актёров</h3>
            <p>Фильмы, рейтинги, отзывы по актёрам</p>
            <div class="report-buttons">
                <a href="?page=report_actors" class="btn-view">👁️ Просмотр</a>
                <a href="?page=export_excel&action=actors" class="btn-excel">📊 Excel</a>
                <a href="?page=export_word&action=actors" class="btn-word">📝 Word</a>
            </div>
        </div>
    </div>
</div>

<style>
.reports-main {
    text-align: center;
    padding: 40px 20px;
}

.reports-main h1 {
    color: #E94560;
    font-size: 2.5em;
    margin-bottom: 40px;
}

.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    max-width: 1400px;
    margin: 0 auto;
}

.report-card {
    background: rgba(0,0,0,0.3);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid rgba(233, 69, 96, 0.3);
    transition: all 0.3s;
}

.report-card:hover {
    transform: translateY(-10px);
    border-color: #E94560;
    box-shadow: 0 10px 30px rgba(233, 69, 96, 0.3);
}

.report-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

.report-card h3 {
    color: #E94560;
    margin: 15px 0 10px;
    font-size: 1.4em;
}

.report-card p {
    color: #b0b0b0;
    margin-bottom: 25px;
    font-size: 0.95em;
}

.report-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-view {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #E94560, #ff6b6b);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9em;
    transition: all 0.3s;
}

.btn-excel {
    display: inline-block;
    padding: 10px 20px;
    background: #217346;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9em;
    transition: all 0.3s;
}

.btn-word {
    display: inline-block;
    padding: 10px 20px;
    background: #2b579a;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9em;
    transition: all 0.3s;
}

.btn-view:hover, .btn-excel:hover, .btn-word:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
</style>