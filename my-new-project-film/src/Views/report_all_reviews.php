<?php if (!isset($_SESSION['user_id'])) exit; ?>

<div class="report-page">
    <div class="report-header">
        <h1>💬 Все отзывы</h1>
        <p>Полный список всех отзывов пользователей</p>
    </div>
    
    <?php if (empty($reviews)): ?>
        <div class="alert alert-info">Пока нет отзывов</div>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr><th>Фильм</th><th>Пользователь</th><th>Оценка</th><th>Отзыв</th><th>Дата</th></tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><strong><?= e($review['movie_title']) ?></strong></td>
                        <td><?= e($review['user_login']) ?></td>
                        <td><span class="rating-badge">⭐ <?= $review['rating'] ?>/10</span></td>
                        <td><?= nl2br(e(mb_substr($review['text'], 0, 150))) ?>...</td>
                        <td><?= date('d.m.Y', strtotime($review['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>Всего отзывов: <?= count($reviews) ?></p>
    <?php endif; ?>
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