<div class="admin-section">
    <div class="admin-header">
        <h2>💬 Управление отзывами</h2>
        <p>Всего отзывов: <strong><?= count($allReviews) ?></strong></p>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Фильм</th>
                <th>Пользователь</th>
                <th>Оценка</th>
                <th>Текст отзыва</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagedReviews as $review): ?>
                <tr>
                    <td><?= $review['id'] ?></td>
                    <td><strong><?= e($review['movie_title']) ?></strong></td>
                    <td><?= e($review['user_login']) ?></td>
                    <td>
                        <span class="rating-badge" style="border-color: <?= getRatingColor($review['rating']) ?>">
                            ⭐ <?= $review['rating'] ?>/10
                        </span>
                    </td>
                    <td class="review-text-cell"><?= e(mb_substr($review['text'], 0, 80)) ?>...</td>
                    <td><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></td>
                    <td>
                        <button class="btn-edit" onclick="openReviewModal(<?= $review['id'] ?>, '<?= e(addslashes($review['text'])) ?>', <?= $review['rating'] ?>)">✏️</button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить отзыв?')">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                            <button type="submit" name="delete_review" class="btn-delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Пагинация для отзывов -->
    <?php if ($reviewsTotalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $reviewsTotalPages; $i++): ?>
            <a href="?page=admin&tab=reviews&p=<?= $i ?>" class="page-link <?= $i === $reviewsCurrentPage ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Модальное окно редактирования отзыва -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReviewModal()">&times;</span>
        <h2>✏️ Редактировать отзыв</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="review_id" id="edit_review_id">
            
            <div class="form-group">
                <label>Оценка (1-10):</label>
                <select name="review_rating" id="edit_review_rating" required>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> из 10</option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Текст отзыва:</label>
                <textarea name="review_text" id="edit_review_text" rows="5" required minlength="10" maxlength="1000"></textarea>
            </div>
            
            <button type="submit" name="edit_review" class="btn btn-success">💾 Сохранить</button>
        </form>
    </div>
</div>

<script>
function openReviewModal(id, text, rating) {
    document.getElementById('edit_review_id').value = id;
    document.getElementById('edit_review_text').value = text;
    document.getElementById('edit_review_rating').value = rating;
    document.getElementById('reviewModal').style.display = 'block';
}
function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}
</script>