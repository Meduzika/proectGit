<?php if (!isset($_SESSION['user_id'])) exit; ?>

<div class="report-page">
    <div class="report-header">
        <h1>🎬 Отзывы по фильму</h1>
        <p>Выберите фильм для просмотра отзывов</p>
    </div>
    
    <div class="movie-selector">
        <form method="GET" action="">
            <input type="hidden" name="page" value="report_movie_reviews">
            <select name="id" onchange="this.form.submit()" style="padding: 10px; background: #2d1b3a; border: 1px solid #e94560; color: white; border-radius: 8px;">
                <option value="">-- Выберите фильм --</option>
                <?php foreach ($movies as $movie): ?>
                    <option value="<?= $movie['id'] ?>" <?= ($movieId == $movie['id']) ? 'selected' : '' ?>>
                        <?= e($movie['title']) ?> (<?= $movie['year'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    
    <?php if ($selectedMovie): ?>
        <h2 style="color: #e94560;"><?= e($selectedMovie['title']) ?> (<?= $selectedMovie['year'] ?>)</h2>
        <p><strong>Режиссёр:</strong> <?= e($selectedMovie['director']) ?></p>
        
        <?php if (empty($reviews)): ?>
            <p>Пока нет отзывов к этому фильму</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div style="border-left: 3px solid #e94560; margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.05);">
                    <strong>👤 <?= e($review['user_login']) ?></strong> | 
                    <span class="rating-badge">⭐ <?= $review['rating'] ?>/10</span> |
                    📅 <?= date('d.m.Y', strtotime($review['created_at'])) ?>
                    <p><?= nl2br(e($review['text'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.report-page { background: rgba(0,0,0,0.3); border-radius: 15px; padding: 30px; border: 2px solid rgba(233,69,96,0.3); }
.report-header { text-align: center; margin-bottom: 30px; }
.report-header h1 { color: #E94560; margin: 0 0 10px 0; }
.report-header p { color: #b0b0b0; }
.movie-selector { text-align: center; margin-bottom: 30px; }
.rating-badge { display: inline-block; padding: 4px 12px; background: rgba(233,69,96,0.2); border-radius: 12px; }
</style>