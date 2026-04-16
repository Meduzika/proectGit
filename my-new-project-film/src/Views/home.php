<?php
// Переменные из index.php: $movies, $topMovies
?>

<div class="hero">
    <h1>🎬 Добро пожаловать в КиноОтзыв</h1>
    <p>Лучшие фильмы, честные отзывы, актуальные рейтинги</p>
</div>

<div class="section">
    <h2>🔥 Популярные фильмы</h2>
    <div class="movies-grid">
        <?php foreach ($topMovies as $movie): ?>
            <div class="movie-card">
                <div class="movie-card-header">
                    <h3><?= e($movie->title) ?></h3>
                    <span class="year"><?= e($movie->year) ?></span>
                </div>
                <div class="movie-card-body">
                    <p><strong>🎥</strong> <?= e($movie->director) ?></p>
                    <p class="description"><?= e(mb_substr($movie->description, 0, 100)) ?>...</p>
                </div>
                <div class="movie-card-footer">
                    <a href="?page=movie&id=<?= $movie->id ?>" class="btn">Подробнее →</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align: center; margin-top: 30px;">
        <a href="?page=movies" class="btn" style="padding: 15px 40px; font-size: 1.1em;">Смотреть все фильмы</a>
    </div>
</div>

<style>
.hero {
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.2), rgba(45, 27, 58, 0.2));
    border-radius: 15px;
    margin-bottom: 40px;
    border: 2px solid #e94560;
}

.hero h1 {
    font-size: 3em;
    color: #e94560;
    margin-bottom: 15px;
}

.hero p {
    font-size: 1.3em;
    color: #b0b0b0;
}

.section {
    background: rgba(0,0,0,0.3);
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    border: 1px solid #e94560;
}

.section h2 {
    color: #e94560;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e94560;
}

.movies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.movie-card {
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid rgba(233, 69, 96, 0.3);
    transition: transform 0.3s;
}

.movie-card:hover {
    transform: translateY(-5px);
}

.movie-card-header {
    background: linear-gradient(135deg, #e94560, #2d1b3a);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.movie-card-header h3 {
    margin: 0;
    font-size: 1.3em;
}

.year {
    background: rgba(0,0,0,0.3);
    padding: 5px 12px;
    border-radius: 15px;
}

.movie-card-body {
    padding: 20px;
}

.description {
    color: #b0b0b0;
    font-size: 0.95em;
}

.movie-card-footer {
    padding: 20px;
    background: rgba(0,0,0,0.2);
}
</style>