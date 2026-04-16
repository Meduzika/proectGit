<div class="admin-section">
    <div class="admin-header">
        <h2>🎭 Управление актёрами</h2>
        <div class="admin-toolbar">
            <button class="btn-add" onclick="openActorModal()">+ Добавить актёра</button>
            <div class="total-count">Всего: <strong><?= count($allActors) ?></strong> актёров</div>
        </div>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя актёра</th>
                <th>Дата рождения</th>
                <th>Фильмы</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagedActors as $actor): ?>
                <tr>
                    <td><?= $actor['id'] ?></td>
                    <td><strong><?= e($actor['name']) ?></strong></td>
                    <td><?= $actor['birth_date'] ? date('d.m.Y', strtotime($actor['birth_date'])) : '—' ?></td>
                    <td>
                        <button class="btn-view" onclick="showActorMovies(<?= $actor['id'] ?>, '<?= e(addslashes($actor['name'])) ?>')">🎬 Показать фильмы</button>
                    </td>
                    <td>
                        <button class="btn-edit" onclick="openEditActorModal(<?= $actor['id'] ?>, '<?= e(addslashes($actor['name'])) ?>', '<?= $actor['birth_date'] ?>')">✏️</button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить актёра?')">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="actor_id" value="<?= $actor['id'] ?>">
                            <button type="submit" name="delete_actor" class="btn-delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Пагинация для актёров -->
    <?php if ($actorsTotalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $actorsTotalPages; $i++): ?>
            <a href="?page=admin&tab=actors&p=<?= $i ?>" class="page-link <?= $i === $actorsCurrentPage ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Модальное окно добавления/редактирования актёра -->
<div id="actorModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeActorModal()">&times;</span>
        <h2 id="actorModalTitle">➕ Добавить актёра</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="actor_id" id="actor_id">
            
            <div class="form-group">
                <label>Имя актёра: <span class="required">*</span></label>
                <input type="text" name="actor_name" id="actor_name" required placeholder="Например: Роберт Дауни мл.">
            </div>
            
            <div class="form-group">
                <label>Дата рождения:</label>
                <input type="date" name="actor_birth_date" id="actor_birth_date">
            </div>
            
            <button type="submit" name="save_actor" class="btn btn-success">💾 Сохранить</button>
        </form>
    </div>
</div>

<!-- Модальное окно с фильмами актёра -->
<div id="actorMoviesModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <span class="close" onclick="closeActorMoviesModal()">&times;</span>
        <h2 id="actorMoviesTitle">🎬 Фильмы с участием</h2>
        <div id="actorMoviesList"></div>
    </div>
</div>

<script>
function openActorModal() {
    document.getElementById('actorModalTitle').innerHTML = '➕ Добавить актёра';
    document.getElementById('actor_id').value = '';
    document.getElementById('actor_name').value = '';
    document.getElementById('actor_birth_date').value = '';
    document.getElementById('actorModal').style.display = 'block';
}

function openEditActorModal(id, name, birthDate) {
    document.getElementById('actorModalTitle').innerHTML = '✏️ Редактировать актёра';
    document.getElementById('actor_id').value = id;
    document.getElementById('actor_name').value = name;
    document.getElementById('actor_birth_date').value = birthDate;
    document.getElementById('actorModal').style.display = 'block';
}

function closeActorModal() {
    document.getElementById('actorModal').style.display = 'none';
}

function showActorMovies(actorId, actorName) {
    document.getElementById('actorMoviesTitle').innerHTML = '🎬 Фильмы с участием: ' + actorName;
    document.getElementById('actorMoviesList').innerHTML = '<div style="text-align:center; padding:20px;">Загрузка...</div>';
    document.getElementById('actorMoviesModal').style.display = 'block';
    
    // AJAX запрос для получения фильмов актёра
    fetch('?page=admin&tab=actors&action=get_movies&actor_id=' + actorId)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('actorMoviesList').innerHTML = '<p style="text-align:center; padding:20px;">Нет фильмов с этим актёром</p>';
                return;
            }
            let html = '<ul style="list-style:none; padding:0;">';
            data.forEach(movie => {
                html += `<li style="padding:10px; border-bottom:1px solid #333;">
                            <strong>${movie.title}</strong> (${movie.year}) — роль: ${movie.role_in_movie || 'не указана'}
                         </li>`;
            });
            html += '</ul>';
            document.getElementById('actorMoviesList').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('actorMoviesList').innerHTML = '<p style="text-align:center; padding:20px; color:red;">Ошибка загрузки</p>';
        });
}

function closeActorMoviesModal() {
    document.getElementById('actorMoviesModal').style.display = 'none';
}
</script>