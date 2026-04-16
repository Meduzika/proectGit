<div class="about-page">
    <div class="about-header">
        <h1>ℹ️ О проекте «КиноОтзыв»</h1>
    </div>
    
    <div class="about-content">
        <div class="about-section">
            <h2>📋 Описание</h2>
            <p><strong>КиноОтзыв</strong> — веб-приложение для просмотра информации о фильмах, актёрах и оставления отзывов.</p>
        </div>
        
        <div class="about-section">
            <h2>🎯 Функционал</h2>
            <ul class="feature-list">
                <li>📋 Просмотр списка фильмов</li>
                <li>🎬 Детальная информация о фильме</li>
                <li>🎭 Информация об актёрах</li>
                <li>💬 Отзывы пользователей</li>
                <li>🏆 TOP-10 фильмов</li>
            </ul>
        </div>
        
        <div class="about-section">
            <h2>🔒 Безопасность</h2>
            <ul class="feature-list">
                <li>✅ Защита от SQL-инъекций (PDO)</li>
                <li>✅ Защита от XSS (htmlspecialchars)</li>
                <li>✅ CSRF-токены</li>
                <li>✅ Валидация данных</li>
            </ul>
        </div>
        
        <div class="success-box">
            <h3>✅ Модуль 2 — MVP реализован!</h3>
            <p>Пункты 2.1 - 2.9 выполнены</p>
        </div>
    </div>
</div>

<style>
.about-page {
    max-width: 900px;
    margin: 0 auto;
}

.about-header {
    text-align: center;
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.2), rgba(45, 27, 58, 0.2));
    color: #e94560;
    padding: 40px;
    border-radius: 15px;
    margin-bottom: 30px;
    border: 2px solid #e94560;
}

.about-header h1 {
    margin: 0;
    font-size: 2.5em;
}

.about-content {
    background: rgba(0,0,0,0.3);
    border-radius: 15px;
    padding: 40px;
    border: 1px solid rgba(233, 69, 96, 0.3);
}

.about-section {
    margin-bottom: 30px;
}

.about-section h2 {
    color: #e94560;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(233, 69, 96, 0.5);
}

.about-section p {
    line-height: 1.8;
    color: #e0e0e0;
}

.feature-list {
    list-style: none;
    padding: 0;
}

.feature-list li {
    padding: 10px 0;
    padding-left: 30px;
    position: relative;
}

.feature-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #2ed573;
    font-weight: bold;
}

.success-box {
    background: linear-gradient(135deg, rgba(46, 213, 115, 0.2), rgba(45, 27, 58, 0.2));
    color: #2ed573;
    padding: 30px;
    border-radius: 10px;
    border: 2px solid #2ed573;
    text-align: center;
    margin-top: 30px;
}

.success-box h3 {
    margin-bottom: 10px;
    font-size: 1.5em;
}
</style>