<div class="admin-section">
    <div class="admin-header">
        <h2>📊 Отчёты</h2>
        <p>Статистика и аналитика по фильмам, отзывам и актёрам</p>
    </div>
    
    <!-- Статистика -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🎬</div>
            <div class="stat-number"><?= $totalMovies ?? 0 ?></div>
            <div class="stat-label">Всего фильмов</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-number"><?= $totalReviews ?? 0 ?></div>
            <div class="stat-label">Всего отзывов</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎭</div>
            <div class="stat-number"><?= $totalActors ?? 0 ?></div>
            <div class="stat-label">Всего актёров</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👑</div>
            <div class="stat-number"><?= $totalUsers ?? 0 ?></div>
            <div class="stat-label">Пользователей</div>
        </div>
    </div>
    
    <!-- TOP-10 фильмов -->
    <div class="reports-section">
        <h3>🏆 TOP-10 фильмов по рейтингу</h3>
        <table class="top-table">
            <thead>
                <tr><th>#</th><th>Название</th><th>Год</th><th>Рейтинг</th><th>Отзывы</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($top10Movies)): ?>
                    <?php foreach ($top10Movies as $index => $movie): ?>
                        <tr>
                            <td class="rank"><?= $index + 1 ?></td>
                            <td><a href="?page=movie&id=<?= $movie['id'] ?>" class="movie-link"><?= e($movie['title']) ?></a></td>
                            <td><?= e($movie['year']) ?></td>
                            <td><span class="rating-badge">⭐ <?= round($movie['avg_rating'], 1) ?></span></td>
                            <td><?= $movie['review_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">Нет данных</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Самые активные пользователи -->
    <div class="reports-section">
        <h3>👥 Самые активные пользователи</h3>
        <table class="top-table">
            <thead>
                <tr><th>Пользователь</th><th>Количество отзывов</th><th>Средний рейтинг</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($topUsers)): ?>
                    <?php foreach ($topUsers as $user): ?>
                        <tr>
                            <td><?= e($user['login']) ?></td>
                            <td><?= $user['review_count'] ?></td>
                            <td><span class="rating-badge">⭐ <?= round($user['avg_rating'], 1) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center;">Нет данных</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}
.stat-card {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    border: 1px solid rgba(233, 69, 96, 0.3);
}
.stat-icon {
    font-size: 3em;
    margin-bottom: 10px;
}
.stat-number {
    font-size: 2.5em;
    font-weight: bold;
    color: #e94560;
}
.stat-label {
    color: #888;
    margin-top: 5px;
}
.reports-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid rgba(233, 69, 96, 0.3);
}
.reports-section h3 {
    color: #e94560;
    margin-bottom: 20px;
}
.top-table {
    width: 100%;
    border-collapse: collapse;
}
.top-table th {
    padding: 12px;
    text-align: left;
    background: rgba(233, 69, 96, 0.2);
    color: #e0e0e0;
}
.top-table td {
    padding: 10px 12px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.rank {
    font-weight: bold;
    color: #e94560;
    width: 50px;
}
.movie-link {
    color: #e94560;
    text-decoration: none;
}
.movie-link:hover {
    text-decoration: underline;
}
.rating-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    background: rgba(233, 69, 96, 0.2);
    border: 1px solid #e94560;
}
</style>