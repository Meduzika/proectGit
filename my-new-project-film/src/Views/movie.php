<?php
// Переменные из index.php: $movie, $reviews
?>

<div class="movie-page">
    <div class="breadcrumbs">
        <a href="?page=home">Главная</a> / 
        <a href="?page=movies">Каталог</a> / 
        <span><?= e($movie->title) ?></span>
    </div>

    <div class="movie-info">
        <h1 class="movie-title"><?= e($movie->title) ?></h1>
        
        <div class="movie-meta">
            <div class="meta-item">
                <div class="meta-label">📅 Год выпуска</div>
                <div class="meta-value"><?= e($movie->year) ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">🎥 Режиссёр</div>
                <div class="meta-value"><?= e($movie->director) ?></div>
            </div>
        </div>
        
        <h3 class="section-title">📝 Описание</h3>
        <div class="description-box">
            <?= nl2br(e($movie->description)) ?>
        </div>
    </div>

    <div class="actors-section">
        <h2 class="section-title">🎭 Актёры и роли</h2>
        <?php if (empty($movie->actors)): ?>
            <p class="no-data">Актёры не указаны</p>
        <?php else: ?>
            <?php foreach ($movie->actors as $actor): ?>
                <div class="actor-row">
                    <div class="actor-name"><?= e($actor['name']) ?></div>
                    <div class="arrow">→</div>
                    <div class="actor-role"><?= e($actor['role_in_movie'] ?? 'Не указана') ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="reviews-section">
        <h2 class="section-title">💬 Отзывы (<?= count($reviews) ?>)</h2>
        
        <?php if (empty($reviews)): ?>
            <p class="no-data">Пока нет отзывов. Будьте первым!</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-user">👤 <?= e($review['user_login']) ?></div>
                        <div class="review-rating">⭐ <?= $review['rating'] ?>/10</div>
                    </div>
                    <div class="review-text"><?= nl2br(e($review['text'])) ?></div>
                    <div class="review-date">📅 <?= e($review['created_at']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="review-form-section">
        <h2 class="section-title">✍️ Оставить отзыв</h2>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                
                <div class="form-group">
                    <label>Ваша оценка: <span class="required">*</span></label>
                    <select name="review_rating" required>
                        <option value="">Выберите оценку</option>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?> из 10</option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Текст отзыва: <span class="required">*</span></label>
                    <textarea name="review_text" required minlength="10" maxlength="1000" placeholder="Расскажите, что вам понравилось в фильме..."></textarea>
                    <small>Минимум 10 символов, максимум 1000</small>
                </div>
                
                <button type="submit" name="submit_review" class="btn">🌸 Оставить отзыв</button>
            </form>
        <?php else: ?>
            <p class="auth-note">🔒 <a href="?page=login" style="color: #e94560;">Войдите</a>, чтобы оставить отзыв</p>
        <?php endif; ?>
    </div>

    <div class="nav-buttons">
        <a href="?page=movies" class="btn btn-secondary">← Все фильмы</a>
    </div>
</div>

<style>
.movie-page {
    background: rgba(0,0,0,0.3);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid rgba(233, 69, 96, 0.3);
}

.breadcrumbs {
    margin-bottom: 25px;
    padding: 10px 0;
    color: #b0b0b0;
}

.breadcrumbs a {
    color: #e94560;
    text-decoration: none;
}

.movie-info {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    border: 1px solid rgba(233, 69, 96, 0.2);
}

.movie-title {
    font-size: 2.2em;
    color: #e94560;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(233, 69, 96, 0.3);
}

.movie-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.meta-item {
    background: rgba(233, 69, 96, 0.1);
    padding: 12px 15px;
    border-radius: 8px;
    border-left: 3px solid #e94560;
}

.meta-label {
    color: #888;
    font-size: 0.9em;
    margin-bottom: 5px;
}

.meta-value {
    font-weight: 600;
    color: #e0e0e0;
}

.description-box {
    background: rgba(255,255,255,0.05);
    padding: 20px;
    border-radius: 10px;
    line-height: 1.8;
    border: 1px dashed rgba(233, 69, 96, 0.3);
}

.section-title {
    font-size: 1.5em;
    color: #e94560;
    margin: 30px 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(233, 69, 96, 0.3);
}

.actors-section, .reviews-section, .review-form-section {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    border: 1px solid rgba(233, 69, 96, 0.2);
}

.actor-row {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px dashed rgba(233, 69, 96, 0.2);
}

.actor-row:last-child {
    border-bottom: none;
}

.actor-name {
    min-width: 200px;
    font-weight: 600;
}

.arrow {
    color: #e94560;
    margin: 0 15px;
    font-size: 1.2em;
}

.actor-role {
    color: #b0b0b0;
}

.review-card {
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 4px solid #e94560;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.review-user {
    font-weight: 600;
    color: #e94560;
}

.review-rating {
    background: rgba(233, 69, 96, 0.2);
    padding: 5px 15px;
    border-radius: 15px;
    border: 1px solid #e94560;
    font-weight: bold;
}

.review-text {
    line-height: 1.6;
    margin-bottom: 10px;
}

.review-date {
    color: #666;
    font-size: 0.85em;
}

.auth-note {
    color: #888;
    font-size: 0.9em;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.required {
    color: #e94560;
}

.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(233, 69, 96, 0.3);
    border-radius: 8px;
    color: #e0e0e0;
    font-size: 1em;
    font-family: inherit;
}

.form-group textarea {
    resize: vertical;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #888;
    font-size: 0.85em;
}

.nav-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.no-data {
    text-align: center;
    color: #888;
    padding: 40px;
    font-style: italic;
}
</style>