<?php if (!isset($_SESSION['user_id'])) exit; ?>

<div class="report-page">
    <div class="report-header">
        <h1>👥 Статистика пользователей</h1>
        <p>Активность и рейтинги пользователей</p>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr><th>Пользователь</th><th>Роль</th><th>Отзывов</th><th>Средний рейтинг</th></tr>
        </thead>
        <tbody>
            <?php foreach ($userStats as $user): ?>
                <tr>
                    <td><strong><?= e($user['login']) ?></strong></td>
                    <td><?= $user['role'] === 'admin' ? '👑 Админ' : '👤 Пользователь' ?></td>
                    <td><?= $user['total_reviews'] ?></td>
                    <td><span class="rating-badge">⭐ <?= round($user['avg_rating'], 1) ?>/10</span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>Всего пользователей: <?= count($userStats) ?></p>
</div>

<style>
.report-page { background: rgba(0,0,0,0.3); border-radius: 15px; padding: 30px; border: 2px solid rgba(233,69,96,0.3); }
.report-header { text-align: center; margin-bottom: 30px; }
.report-header h1 { color: #E94560; margin: 0 0 10px 0; }
.report-header p { color: #b0b0b0; }
.admin-table { width: 100%; border-collapse: collapse; }
.admin-table th { background: #e94560; padding: 12px; text-align: left; color: white; }
.admin-table td { padding: 12px; border-bottom: 1px solid rgba(233,69,96,0.2); }
.rating-badge { display: inline-block; padding: 4px 12px; background: rgba(233,69,96,0.2); border-radius: 12px; }
</style>