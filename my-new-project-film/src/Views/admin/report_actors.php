<h1>🎭 Статистика по актёрам</h1>

<table class="admin-table">
    <thead>
        <tr>
            <th>Актёр</th>
            <th>Дата рождения</th>
            <th>Фильмов</th>
            <th>Ср. рейтинг</th>
            <th>Отзывов</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($actors as $actor): ?>
            <tr>
                <td><?= e($actor['name']) ?></td>
                <td><?= e($actor['birth_date']) ?></td>
                <td><?= $actor['films_count'] ?></td>
                <td><strong><?= $actor['avg_rating'] ?? '0' ?></strong></td>
                <td><?= $actor['reviews_count'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="?page=admin" class="btn btn-secondary">← Назад в админку</a>

<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255,255,255,0.05);
    margin: 20px 0;
}
.admin-table th, .admin-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid rgba(233, 69, 96, 0.2);
}
.admin-table th {
    background: rgba(233, 69, 96, 0.3);
    font-weight: 600;
}
</style>