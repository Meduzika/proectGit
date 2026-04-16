<?php
// Переменная $movies доступна из контроллера
?>
<div class="export-page">
    <div class="export-header">
        <h1>📄 Выгрузка отчётов</h1>
        <p>Доступно только для авторизованных пользователей</p>
    </div>
    
    <div class="export-grid">
        <div class="export-card">
            <div class="export-icon">📊</div>
            <h3>Excel отчёты (CSV)</h3>
            <div class="export-buttons">
                <a href="?page=export&action=excel_top10" class="btn-excel">🏆 TOP-10 фильмов</a>
                <a href="?page=export&action=excel_reviews" class="btn-excel">💬 Все отзывы</a>
                <a href="?page=export&action=excel_users" class="btn-excel">👥 Статистика пользователей</a>
            </div>
        </div>
        
        <div class="export-card">
            <div class="export-icon">📝</div>
            <h3>Word отчёты (DOC)</h3>
            <div class="export-buttons">
                <a href="?page=export&action=word_top10" class="btn-word">🏆 TOP-10 фильмов</a>
                <div class="movie-select">
                    <select id="movieSelect" class="movie-select-input">
                        <option value="">-- Выберите фильм --</option>
                        <?php foreach ($movies as $movie): ?>
                            <option value="<?= $movie['id'] ?>"><?= htmlspecialchars($movie['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button onclick="exportMovieReviews()" class="btn-word" style="margin-top:10px;">🎬 Отзывы по фильму</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.export-page { max-width: 1200px; margin: 0 auto; }
.export-header { text-align: center; padding: 40px; background: linear-gradient(135deg, rgba(233,69,96,0.2), rgba(45,27,58,0.2)); border-radius: 15px; margin-bottom: 40px; border: 2px solid #e94560; }
.export-header h1 { font-size: 2.5em; color: #e94560; }
.export-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; }
.export-card { background: rgba(0,0,0,0.3); border-radius: 15px; padding: 30px; text-align: center; border: 1px solid rgba(233,69,96,0.3); }
.export-icon { font-size: 4em; }
.export-card h3 { color: #e94560; margin-bottom: 20px; }
.export-buttons { display: flex; flex-direction: column; gap: 12px; }
.btn-excel, .btn-word { display: block; padding: 12px; background: rgba(233,69,96,0.1); border: 1px solid #e94560; border-radius: 8px; color: #e0e0e0; text-decoration: none; }
.btn-excel:hover { background: #1e8449; }
.btn-word:hover { background: #2c3e50; }
.movie-select-input { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid #e94560; border-radius: 8px; color: #e0e0e0; }
</style>

<script>
function exportMovieReviews() {
    var movieId = document.getElementById('movieSelect').value;
    if (movieId) window.location.href = '?page=export&action=word_movie&movie_id=' + movieId;
    else alert('Выберите фильм');
}
</script>