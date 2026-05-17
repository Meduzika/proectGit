<?php

echo "<div style='background:lime; color:black; padding:20px; font-size:40px; text-align:center;'>🔥 ФАЙЛ export.php ЗАГРУЖЕН!</div>";
// exit;  ← НЕ РАСКОММЕНТИРУЙ ЭТУ СТРОКУ!
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-error">🔒 Доступно только для авторизованных пользователей</div>';
    exit;
}

$movies = \Models\Movie::getAll($pdo);
?>

<div class="export-page">
    <div class="export-header">
        <h1>📊 Выгрузка отчётов</h1>
        <p>Каждый отчёт доступен в Excel и Word</p>
    </div>
    
    <div class="reports-list">
        <!-- TOP-10 -->
        <div class="report-item">
            <div class="report-icon">🏆</div>
            <div class="report-info">
                <h3>TOP-10 фильмов</h3>
                <p>Лучшие фильмы по рейтингу</p>
            </div>
            <div class="report-actions">
                <a href="?page=export_excel&action=top10" class="btn-excel">📊 Excel</a>
                <a href="?page=export_word&action=top10" class="btn-word">📝 Word</a>
            </div>
        </div>
        
        <!-- Все отзывы -->
        <div class="report-item">
            <div class="report-icon">💬</div>
            <div class="report-info">
                <h3>Все отзывы</h3>
                <p>Полный список отзывов</p>
            </div>
            <div class="report-actions">
                <a href="?page=export_excel&action=all_reviews" class="btn-excel">📊 Excel</a>
                <a href="?page=export_word&action=all_reviews" class="btn-word">📝 Word</a>
            </div>
        </div>
        
        <!-- Статистика пользователей -->
        <div class="report-item">
            <div class="report-icon">👥</div>
            <div class="report-info">
                <h3>Статистика пользователей</h3>
                <p>Активность по пользователям</p>
            </div>
            <div class="report-actions">
                <a href="?page=export_excel&action=user_stats" class="btn-excel">📊 Excel</a>
                <a href="?page=export_word&action=user_stats" class="btn-word">📝 Word</a>
            </div>
        </div>
        
        <!-- Отзывы по фильму -->
        <div class="report-item">
            <div class="report-icon">🎬</div>
            <div class="report-info">
                <h3>Отзывы по фильму</h3>
                <p>Выберите фильм для отчёта</p>
                <select id="movieSelect" class="movie-select">
                    <option value="">-- Выберите фильм --</option>
                    <?php foreach ($movies as $movie): ?>
                        <option value="<?= $movie->id ?>"><?= e($movie->title) ?> (<?= $movie->year ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="report-actions">
                <a href="#" onclick="exportMovie('excel')" class="btn-excel">📊 Excel</a>
                <a href="#" onclick="exportMovie('word')" class="btn-word">📝 Word</a>
            </div>
        </div>
    </div>
</div>

<style>
.export-page { max-width: 1000px; margin: 0 auto; padding: 20px; }
.export-header { text-align: center; margin-bottom: 40px; padding: 30px; background: linear-gradient(135deg, rgba(233,69,96,0.2), rgba(45,27,58,0.2)); border-radius: 15px; border: 2px solid #E94560; }
.export-header h1 { color: #E94560; font-size: 2.5em; margin: 0 0 10px 0; }
.reports-list { display: flex; flex-direction: column; gap: 20px; }
.report-item { display: flex; align-items: center; gap: 25px; padding: 25px; background: rgba(0,0,0,0.3); border-radius: 12px; border: 1px solid rgba(233,69,96,0.3); }
.report-icon { font-size: 3em; width: 80px; text-align: center; }
.report-info { flex: 1; }
.report-info h3 { color: #E94560; margin: 0 0 8px 0; font-size: 1.3em; }
.report-info p { color: #b0b0b0; margin: 0 0 10px 0; }
.movie-select { width: 100%; max-width: 300px; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(233,69,96,0.3); border-radius: 6px; color: #e0e0e0; margin-top: 10px; }
.report-actions { display: flex; gap: 12px; min-width: 220px; }
.btn-excel, .btn-word { padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; min-width: 100px; text-align: center; transition: all 0.3s; }
.btn-excel { background: linear-gradient(135deg, #217346, #32a852); color: white; }
.btn-word { background: linear-gradient(135deg, #2b579a, #4472c4); color: white; }
.btn-excel:hover, .btn-word:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
</style>

<script>
function exportMovie(format) {
    const movieId = document.getElementById('movieSelect').value;
    if (!movieId) { alert('⚠️ Выберите фильм!'); return; }
    const page = format === 'excel' ? 'export_excel' : 'export_word';
    window.location.href = `?page=${page}&action=movie_reviews&movie_id=${movieId}`;
}
</script>