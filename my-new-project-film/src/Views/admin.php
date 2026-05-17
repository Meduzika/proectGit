<?php
// Переменные из index.php: $movies, $pagedMovies, $totalMovies, $currentPage, $totalPages
?>

<div class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">⚙️ Админ-панель</h1>
    </div>
    
    <div class="admin-tabs">
        <a href="?page=admin&tab=movies" class="tab active">🎬 Фильмы</a>
        <a href="?page=admin&tab=reviews" class="tab">💬 Отзывы</a>
        <a href="?page=admin&tab=actors" class="tab">🎭 Актёры</a>
        <a href="?page=admin&tab=reports" class="tab">📊 Отчёты</a>
    </div>
    
    <div class="admin-toolbar">
        <button class="btn-add" onclick="openAddModal()">+ Добавить фильм</button>
        <div class="total-count">Всего: <strong><?= $totalMovies ?></strong> фильма</div>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Год</th>
                <th>Режиссёр</th>
                <th>Отзывы</th>
                <th>Рейтинг</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagedMovies as $movie): 
                $reviews = \Models\Review::getByMovie($pdo, $movie->id);
                $avgRating = 0;
                if (!empty($reviews)) {
                    $total = array_sum(array_column($reviews, 'rating'));
                    $avgRating = round($total / count($reviews), 1);
                }
            ?>
                <tr>
                    <td><?= $movie->id ?></td>
                    <td><?= e($movie->title) ?></td>
                    <td><?= e($movie->year) ?></td>
                    <td><?= e($movie->director) ?></td>
                    <td><?= count($reviews) ?></td>
                    <td>
                        <span class="rating-badge-admin" style="border-color: <?= getRatingColor($avgRating) ?>; color: <?= getRatingColor($avgRating) ?>">
                            <?= $avgRating ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-edit" onclick="openEditModal(<?= $movie->id ?>, '<?= e($movie->title) ?>', <?= $movie->year ?>, '<?= e($movie->director) ?>', '<?= e($movie->description) ?>')" title="Редактировать">✏️</button>
                            
                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('⚠️ Удалить фильм «<?= e($movie->title) ?>»?');">
                                <input type="hidden" name="movie_id" value="<?= $movie->id ?>">
                                <button type="submit" name="delete_movie" class="btn-icon btn-delete" title="Удалить">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=admin&p=<?= $i ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    
    <div class="reports-section">
        <h3 class="reports-title">📊 Отчёты</h3>
        <div class="reports-buttons">
            <a href="?page=top10" class="btn-report">📋 TOP-10</a>
            <a href="#" class="btn-report">📄 Excel</a>
            <a href="#" class="btn-report">📄 Word</a>
        </div>
    </div>
</div>

<!-- Модальное окно ДОБАВЛЕНИЯ -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h2>➕ Добавить фильм</h2>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label>Название: <span class="required">*</span></label>
                <input type="text" name="title" required placeholder="Например: Железный человек">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Год: <span class="required">*</span></label>
                    <input type="number" name="year" required min="1900" max="2025" placeholder="2008">
                </div>
                
                <div class="form-group">
                    <label>Режиссёр: <span class="required">*</span></label>
                    <input type="text" name="director" required placeholder="Джон Фавро">
                </div>
            </div>
            
            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" rows="4" placeholder="Краткое описание фильма..."></textarea>
            </div>
            
            <button type="submit" name="add_movie" class="btn btn-success">💾 Сохранить</button>
        </form>
    </div>
</div>

<!-- Модальное окно РЕДАКТИРОВАНИЯ -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>✏️ Редактировать фильм</h2>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="movie_id" id="edit_movie_id">
            
            <div class="form-group">
                <label>Название: <span class="required">*</span></label>
                <input type="text" name="title" id="edit_title" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Год: <span class="required">*</span></label>
                    <input type="number" name="year" id="edit_year" required min="1900" max="2025">
                </div>
                
                <div class="form-group">
                    <label>Режиссёр: <span class="required">*</span></label>
                    <input type="text" name="director" id="edit_director" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" id="edit_description" rows="4"></textarea>
            </div>
            
            <button type="submit" name="edit_movie" class="btn btn-success">💾 Сохранить изменения</button>
        </form>
    </div>
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
.admin-page {
    background: rgba(0,0,0,0.3);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid rgba(233, 69, 96, 0.3);
}

.admin-header {
    margin-bottom: 30px;
}

.admin-title {
    font-size: 2em;
    color: #e94560;
}

.admin-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.tab {
    padding: 10px 20px;
    background: rgba(233, 69, 96, 0.1);
    border: 1px solid #e94560;
    border-radius: 8px;
    color: #e0e0e0;
    text-decoration: none;
    transition: all 0.3s;
}

.tab:hover, .tab.active {
    background: #e94560;
    color: white;
}

.admin-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.btn-add {
    padding: 12px 25px;
    background: linear-gradient(135deg, #2ed573, #1dd1a1);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1em;
    font-weight: 600;
}

.total-count {
    color: #b0b0b0;
    font-size: 1.1em;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 25px;
}

.admin-table thead {
    background: linear-gradient(135deg, #e94560, #2d1b3a);
}

.admin-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: white;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid rgba(233, 69, 96, 0.1);
}

.admin-table tbody tr {
    transition: background 0.3s;
}

.admin-table tbody tr:hover {
    background: rgba(233, 69, 96, 0.05);
}

.rating-badge-admin {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 12px;
    font-weight: bold;
    font-size: 0.9em;
    border: 1px solid;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-icon {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9em;
}

.btn-edit {
    background: rgba(255, 165, 2, 0.2);
    color: #ffa502;
    border: 1px solid #ffa502;
}

.btn-delete {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 1px solid #ff6b6b;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 25px 0;
}

.page-link {
    padding: 10px 16px;
    background: rgba(233, 69, 96, 0.1);
    border: 1px solid #e94560;
    border-radius: 50%;
    color: #e0e0e0;
    text-decoration: none;
}

.page-link:hover, .page-link.active {
    background: #e94560;
    color: white;
}

.reports-section {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid rgba(233, 69, 96, 0.3);
}

.reports-title {
    font-size: 1.3em;
    color: #e94560;
    margin-bottom: 15px;
}

.reports-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-report {
    padding: 12px 25px;
    background: rgba(233, 69, 96, 0.2);
    border: 2px solid #e94560;
    border-radius: 8px;
    color: #e0e0e0;
    text-decoration: none;
}

.btn-report:hover {
    background: #e94560;
    color: white;
}

/* Модальные окна */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background: linear-gradient(135deg, #2d1b3a 0%, #1a1a2e 100%);
    margin: 5% auto;
    padding: 30px;
    border: 2px solid #e94560;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    position: relative;
}

.close {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 28px;
    font-weight: bold;
    color: #e94560;
    cursor: pointer;
}

.close:hover {
    color: #ff6b6b;
}

.modal-content h2 {
    color: #e94560;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(233, 69, 96, 0.3);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(233, 69, 96, 0.3);
    border-radius: 8px;
    color: #e0e0e0;
    font-size: 1em;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.required {
    color: #e94560;
}

.btn-success {
    background: linear-gradient(135deg, #2ed573, #1dd1a1);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1em;
    font-weight: 600;
}
</style>

<script>
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openEditModal(id, title, year, director, description) {
    document.getElementById('edit_movie_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_year').value = year;
    document.getElementById('edit_director').value = director;
    document.getElementById('edit_description').value = description;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>