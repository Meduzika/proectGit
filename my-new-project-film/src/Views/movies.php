<?php
// Переменная из index.php: $movies
?>

<div class="page-header">
    <h1>🎥 Каталог фильмов</h1>
    <p>Всего фильмов: <strong><?= count($movies) ?></strong></p>
</div>

<div class="movies-grid">
    <?php foreach ($movies as $movie): ?>
        <div class="movie-card">
            <div class="movie-card-header">
                <h3><?= e($movie->title) ?></h3>
                <span class="year"><?= e($movie->year) ?></span>
            </div>
            <div class="movie-card-body">
                <p><strong>🎥 Режиссёр:</strong> <?= e($movie->director) ?></p>
                <p class="description"><?= e(mb_substr($movie->description, 0, 120)) ?>...</p>
            </div>
            <div class="movie-card-footer">
                <a href="?page=movie&id=<?= $movie->id ?>" class="btn">📖 Подробнее</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.page-header {
    text-align: center;
    padding: 40px;
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.2), rgba(45, 27, 58, 0.2));
    border-radius: 15px;
    margin-bottom: 30px;
    border: 2px solid #e94560;
}

.page-header h1 {
    font-size: 2.5em;
    color: #e94560;
    margin-bottom: 10px;
}

.page-header p {
    font-size: 1.2em;
    color: #b0b0b0;
}

.movies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
}

.movie-card {
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(233, 69, 96, 0.3);
    transition: all 0.3s;
}

.movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(233, 69, 96, 0.3);
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
    font-size: 1.4em;
}

.year {
    background: rgba(0,0,0,0.3);
    padding: 5px 15px;
    border-radius: 20px;
}

.movie-card-body {
    padding: 20px;
}

.movie-card-body p {
    margin-bottom: 10px;
    line-height: 1.6;
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