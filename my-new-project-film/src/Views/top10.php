<?php
// Переменная из index.php: $top10
?>

<div class="top10-page">
    <div class="top10-header">
        <h1>🏆 Отчёт ТОП-10</h1>
        <p>Лучшие фильмы по рейтингу пользователей</p>
    </div>
    
    <table class="top-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Год</th>
                <th>Рейтинг</th>
                <th>Отзывы</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($top10 as $index => $item): ?>
                <tr>
                    <td class="rank"><?= $index + 1 ?></td>
                    <td>
                        <a href="?page=movie&id=<?= $item['movie']->id ?>" class="movie-link">
                            <?= e($item['movie']->title) ?>
                        </a>
                    </td>
                    <td><?= e($item['movie']->year) ?></td>
                    <td>
                        <span class="rating-badge" style="border-color: <?= getRatingColor($item['rating']) ?>; color: <?= getRatingColor($item['rating']) ?>">
                            <?= $item['rating'] ?>
                        </span>
                    </td>
                    <td class="review-count"><?= $item['review_count'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
function getRatingColor($rating) {
    if ($rating >= 9) return '#e94560';
    if ($rating >= 8) return '#ffa502';
    if ($rating >= 7) return '#2ed573';
    return '#1e90ff';
}
?>

<style>
.top10-page {
    background: rgba(0,0,0,0.3);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid rgba(233, 69, 96, 0.3);
}

.top10-header {
    text-align: center;
    padding: 30px;
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.2), rgba(45, 27, 58, 0.2));
    border-radius: 12px;
    margin-bottom: 30px;
    border: 2px solid #e94560;
}

.top10-header h1 {
    font-size: 2.5em;
    color: #e94560;
    margin-bottom: 10px;
}

.top-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    overflow: hidden;
}

.top-table thead {
    background: linear-gradient(135deg, #e94560, #2d1b3a);
}

.top-table th {
    padding: 18px 15px;
    text-align: left;
    font-weight: 600;
    color: white;
}

.top-table td {
    padding: 15px;
    border-bottom: 1px solid rgba(233, 69, 96, 0.1);
}

.top-table tbody tr {
    transition: all 0.3s;
}

.top-table tbody tr:hover {
    background: rgba(233, 69, 96, 0.05);
}

.top-table tbody tr:nth-child(1) { background: rgba(233, 69, 96, 0.1); }
.top-table tbody tr:nth-child(2) { background: rgba(233, 69, 96, 0.08); }
.top-table tbody tr:nth-child(3) { background: rgba(233, 69, 96, 0.05); }

.rank {
    font-weight: bold;
    font-size: 1.1em;
    color: #e94560;
    width: 40px;
}

.movie-link {
    color: #e94560;
    text-decoration: none;
    font-weight: 600;
}

.movie-link:hover {
    text-decoration: underline;
}

.rating-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 15px;
    font-weight: bold;
    border: 2px solid;
    background: rgba(233, 69, 96, 0.2);
}

.review-count {
    color: #888;
}
</style>