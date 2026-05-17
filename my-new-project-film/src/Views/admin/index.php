<?php
// Переменные из контроллера: $movies, $pagedMovies, $totalMovies, $currentPage, $totalPages, $pdo, $activeTab
$activeTab = $_GET['tab'] ?? 'movies';
?>

<div class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">⚙️ Админ-панель</h1>
    </div>
    
    <div class="admin-tabs">
        <a href="?page=admin&tab=movies" class="tab <?= $activeTab === 'movies' ? 'active' : '' ?>">🎬 Фильмы</a>
        <a href="?page=admin&tab=reviews" class="tab <?= $activeTab === 'reviews' ? 'active' : '' ?>">💬 Отзывы</a>
        <a href="?page=admin&tab=actors" class="tab <?= $activeTab === 'actors' ? 'active' : '' ?>">🎭 Актёры</a>
        <a href="?page=admin&tab=reports" class="tab <?= $activeTab === 'reports' ? 'active' : '' ?>">📊 Отчёты</a>
    </div>
    
    <?php if ($activeTab === 'movies'): ?>
        <!-- ===== УПРАВЛЕНИЕ ФИЛЬМАМИ ===== -->
        <div class="admin-toolbar">
            <button class="btn-add" onclick="openAddModal()">+ Добавить фильм</button>
            <div class="total-count">Всего: <strong><?= $totalMovies ?></strong> фильмов</div>
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
                        <td><span class="rating-badge">⭐ <?= $avgRating ?></span></td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal(<?= $movie->id ?>, '<?= e(addslashes($movie->title)) ?>', <?= $movie->year ?>, '<?= e(addslashes($movie->director)) ?>', '<?= e(addslashes($movie->description)) ?>')">✏️</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить фильм?')">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="movie_id" value="<?= $movie->id ?>">
                                <button type="submit" name="delete_movie" class="btn-delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Пагинация -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=admin&tab=movies&p=<?= $i ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        
    <?php elseif ($activeTab === 'reviews'): ?>
        <!-- Содержимое будет из reviews.php -->
        <p>Загрузка отзывов...</p>
        
    <?php elseif ($activeTab === 'actors'): ?>
        <!-- Содержимое будет из actors.php -->
        <p>Загрузка актёров...</p>
        
    <?php elseif ($activeTab === 'reports'): ?>
        <!-- Содержимое будет из reports.php -->
        <p>Загрузка отчётов...</p>
    <?php endif; ?>
</div>

<!-- Модальное окно ДОБАВЛЕНИЯ -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h2>➕ Добавить фильм</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label>Название: <span class="required">*</span></label>
                <input type="text" name="title" required placeholder="Например: Железный человек">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Год: <span class="required">*</span></label>
                    <input type="number" name="year" required min="1900" max="2025">
                </div>
                <div class="form-group">
                    <label>Режиссёр: <span class="required">*</span></label>
                    <input type="text" name="director" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" rows="4"></textarea>
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
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
            
            <button type="submit" name="edit_movie" class="btn btn-success">💾 Сохранить</button>
        </form>
    </div>
</div>

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
}
.tab:hover, .tab.active {
    background: #e94560;
    color: white;
}
.admin-toolbar {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}
.btn-add {
    padding: 10px 20px;
    background: #2ed573;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
.admin-table {
    width: 100%;
    border-collapse: collapse;
}
.admin-table th, .admin-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.admin-table th {
    background: rgba(233, 69, 96, 0.3);
}
.btn-edit {
    background: #ffa502;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}
.btn-delete {
    background: #ff6b6b;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}
.pagination {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    justify-content: center;
}
.page-link {
    padding: 8px 12px;
    background: rgba(233, 69, 96, 0.2);
    border: 1px solid #e94560;
    border-radius: 5px;
    color: #e0e0e0;
    text-decoration: none;
}
.page-link.active {
    background: #e94560;
}
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 1000;
}
.modal-content {
    background: #2d1b3a;
    margin: 10% auto;
    padding: 30px;
    width: 90%;
    max-width: 500px;
    border-radius: 15px;
    border: 2px solid #e94560;
}
.close {
    float: right;
    font-size: 28px;
    cursor: pointer;
    color: #e94560;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
}
.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px;
    background: rgba(255,255,255,0.1);
    border: 1px solid #e94560;
    border-radius: 5px;
    color: white;
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
    background: #2ed573;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.rating-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    background: rgba(233, 69, 96, 0.2);
    border: 1px solid #e94560;
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