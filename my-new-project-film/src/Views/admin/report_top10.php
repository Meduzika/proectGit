<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo '<div class="alert alert-error">❌ Доступ запрещён!</div>';
    exit;
}
?>

<div class="report-page">
    <div class="report-header">
        <h1>🏆 TOP-10 Фильмов</h1>
        <div class="report-actions">
            <a href="?page=export_excel&action=top10" class="btn-download excel">📊 Скачать Excel</a>
            <a href="?page=export_word&action=top10" class="btn-download word">📝 Скачать Word</a>
            <a href="?page=admin" class="btn-back">← Назад</a>
        </div>
    </div>
    
    <table class="report-table">
        <thead>
            <tr>
                <th>№</th>
                <th>Название</th>
                <th>Год</th>
                <th>Режиссёр</th>
                <th>Рейтинг</th>
                <th>Отзывы</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($top10 as $index => $movie): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><strong><?= e($movie['title']) ?></strong></td>
                    <td><?= e($movie['year']) ?></td>
                    <td><?= e($movie['director']) ?></td>
                    <td><span class="rating"><?= $movie['rating'] ?>/10</span></td>
                    <td><?= $movie['reviews'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.report-page {
    background: rgba(0,0,0,0.3);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid rgba(233, 69, 96, 0.3);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.report-header h1 {
    color: #E94560;
    margin: 0;
}

.report-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-download, .btn-back {
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-block;
}

.btn-download.excel {
    background: linear-gradient(135deg, #217346, #32a852);
    color: white;
}

.btn-download.word {
    background: linear-gradient(135deg, #2b579a, #4472c4);
    color: white;
}

.btn-back {
    background: rgba(255,255,255,0.1);
    color: #e0e0e0;
    border: 1px solid #e94560;
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    overflow: hidden;
}

.report-table thead {
    background: linear-gradient(135deg, #E94560, #2d1b3a);
}

.report-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: white;
}

.report-table td {
    padding: 12px 15px;
    border-bottom: 1px solid rgba(233, 69, 96, 0.1);
}

.report-table tbody tr {
    transition: background 0.3s;
}

.report-table tbody tr:hover {
    background: rgba(233, 69, 96, 0.05);
}

.rating {
    display: inline-block;
    padding: 5px 12px;
    background: rgba(233, 69, 96, 0.2);
    border: 1px solid #E94560;
    border-radius: 15px;
    font-weight: bold;
    color: #E94560;
}
</style>