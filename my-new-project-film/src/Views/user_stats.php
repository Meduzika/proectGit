<div class="report-page">
    <div class="report-header">
        <h1>👥 Статистика пользователей</h1>
        <p>Активность и рейтинги пользователей</p>
        <div class="report-actions">
            <a href="?page=export&action=excel_users_stats" class="btn-download excel">📊 Excel</a>
            <a href="?page=export&action=word_users_stats" class="btn-download word">📝 Word</a>
            <a href="?page=home" class="btn-back">← На главную</a>
        </div>
    </div>
    
    <?php if (empty($userStats)): ?>
        <div class="alert alert-info">Нет данных о пользователях</div>
    <?php else: ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Роль</th>
                    <th>Отзывов</th>
                    <th>Средний рейтинг</th>
                    <th>Первый отзыв</th>
                    <th>Последний отзыв</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userStats as $user): ?>
                    <tr>
                        <td><strong><?= e($user['login']) ?></strong></td>
                        <td><?= $user['role'] === 'admin' ? '👑 Администратор' : '👤 Пользователь' ?></td>
                        <td><?= $user['total_reviews'] ?></td>
                        <td><span class="rating">⭐ <?= round($user['avg_rating'], 1) ?>/10</span></td>
                        <td><?= $user['first_review'] ? date('d.m.Y', strtotime($user['first_review'])) : '—' ?></td>
                        <td><?= $user['last_review'] ? date('d.m.Y', strtotime($user['last_review'])) : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="total-count">Всего пользователей: <?= count($userStats) ?></p>
    <?php endif; ?>
</div>

<style>
/* Стили такие же, как в all_reviews.php */
</style>