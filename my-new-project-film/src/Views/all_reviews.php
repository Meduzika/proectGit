<div class="report-page">
    <div class="report-header">
        <h1>💬 Все отзывы</h1>
        <p>Полный список всех отзывов пользователей</p>
        <div class="report-actions">
            <a href="?page=export&action=excel_all_reviews" class="btn-download excel">📊 Excel</a>
            <a href="?page=export&action=word_all_reviews" class="btn-download word">📝 Word</a>
            <a href="?page=home" class="btn-back">← На главную</a>
        </div>
    </div>
    
    <?php if (empty($reviews)): ?>
        <div class="alert alert-info">Пока нет отзывов</div>
    <?php else: ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Фильм</th>
                    <th>Пользователь</th>
                    <th>Оценка</th>
                    <th>Отзыв</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><strong><?= e($review['movie_title']) ?></strong></td>
                        <td><?= e($review['user_login']) ?></td>
                        <td><span class="rating">⭐ <?= $review['rating'] ?>/10</span></td>
                        <td><?= nl2br(e($review['text'])) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="total-count">Всего отзывов: <?= count($reviews) ?></p>
    <?php endif; ?>
</div>

<style>
.report-page {
    background: rgba(0,0,0,0.3);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid rgba(233, 69, 96, 0.3);
}
.report-header {
    text-align: center;
    margin-bottom: 30px;
}
.report-header h1 {
    color: #E94560;
    margin: 0 0 10px 0;
}
.report-header p {
    color: #b0b0b0;
    margin-bottom: 20px;
}
.report-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-download {
    padding: 10px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    color: white;
}
.btn-download.excel { background: #217346; }
.btn-download.word { background: #2b579a; }
.btn-back {
    padding: 10px 25px;
    background: rgba(255,255,255,0.1);
    border: 1px solid #e94560;
    border-radius: 8px;
    color: #e0e0e0;
    text-decoration: none;
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
    color: white;
}
.report-table td {
    padding: 12px 15px;
    border-bottom: 1px solid rgba(233, 69, 96, 0.1);
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
.total-count {
    margin-top: 20px;
    text-align: right;
    color: #b0b0b0;
}
</style>