<div class="report-page">
    <div class="report-header">
        <h1>🎬 Отзывы к фильму: <?= e($movieReviews['movie']['title']) ?></h1>
        <div class="report-actions">
            <a href="?page=export&action=excel_movie_reviews&movie_id=<?= $_GET['id'] ?>" class="btn-download excel">📊 Excel</a>
            <a href="?page=export&action=word_movie_reviews&movie_id=<?= $_GET['id'] ?>" class="btn-download word">📝 Word</a>
            <a href="?page=home" class="btn-back">← На главную</a>
        </div>
    </div>
    
    <div class="movie-selector">
        <form method="GET" action="">
            <input type="hidden" name="page" value="movie_reviews">
            <select name="id" onchange="this.form.submit()">
                <option value="">-- Выберите фильм --</option>
                <?php foreach ($movies as $movie): ?>
                    <option value="<?= $movie->id ?>" <?= ($_GET['id'] ?? 0) == $movie->id ? 'selected' : ?>>
                        <?= e($movie->title) ?> (<?= $movie->year ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    
    <?php if (empty($movieReviews['reviews'])): ?>
        <div class="alert alert-info">Пока нет отзывов к этому фильму</div>
    <?php else: ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Оценка</th>
                    <th>Отзыв</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movieReviews['reviews'] as $review): ?>
                    <tr>
                        <td><strong><?= e($review['user_login']) ?></strong></td>
                        <td><span class="rating">⭐ <?= $review['rating'] ?>/10</span></td>
                        <td><?= nl2br(e($review['text'])) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="total-count">Всего отзывов: <?= count($movieReviews['reviews']) ?></p>
    <?php endif; ?>
</div>

<style>
.movie-selector {
    margin-bottom: 30px;
    text-align: center;
}
.movie-selector select {
    padding: 12px 20px;
    background: rgba(255,255,255,0.05);
    border: 1px solid #e94560;
    border-radius: 8px;
    color: #e0e0e0;
    font-size: 1em;
    min-width: 250px;
}
</style>