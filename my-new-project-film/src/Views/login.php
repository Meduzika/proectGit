<div class="login-page">
    <div class="login-box">
        <h1>🔐 Вход в систему</h1>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" name="login" required placeholder="admin или ivanov" value="<?= e($_POST['login'] ?? '') ?>" autofocus>
            </div>
            
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required placeholder="password">
            </div>
            
            <button type="submit" name="login_submit" class="btn">Войти</button>
        </form>
        
        <div class="login-hint">
            <strong>📋 Тестовые аккаунты:</strong><br><br>
            <strong>👑 Администратор:</strong><br>
            Логин: <code>admin</code> / Пароль: <code>password</code><br>
            <small>Может управлять фильмами, отзывами, актёрами</small><br><br>
            
            <strong>👤 Пользователь:</strong><br>
            Логин: <code>ivanov</code> / Пароль: <code>password</code><br>
            <small>Может смотреть фильмы и оставлять отзывы</small>
        </div>
    </div>
</div>

<style>
.login-page {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
}

.login-box {
    background: rgba(0,0,0,0.3);
    padding: 40px;
    border-radius: 15px;
    border: 2px solid rgba(233, 69, 96, 0.3);
    width: 100%;
    max-width: 450px;
}

.login-box h1 {
    text-align: center;
    color: #e94560;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-group input {
    width: 100%;
    padding: 12px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(233, 69, 96, 0.3);
    border-radius: 8px;
    color: #e0e0e0;
    font-size: 1em;
}

.btn {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #e94560, #ff6b6b);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1em;
    cursor: pointer;
    font-weight: 600;
}

.login-hint {
    margin-top: 30px;
    padding: 20px;
    background: rgba(233, 69, 96, 0.1);
    border-radius: 8px;
    border-left: 4px solid #e94560;
    font-size: 0.9em;
    color: #b0b0b0;
    line-height: 1.8;
}

.login-hint code {
    background: rgba(233, 69, 96, 0.3);
    padding: 2px 8px;
    border-radius: 4px;
    color: #fff;
    font-weight: bold;
}

.login-hint small {
    display: block;
    margin-top: 5px;
    font-size: 0.85em;
    color: #888;
}
</style>