<?php
require_once __DIR__ . '/../src/bootstrap.php';

if (Auth::user()) {
    redirect('/dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::require();
    $result = Auth::attemptLogin(trim((string)($_POST['email'] ?? '')), (string)($_POST['password'] ?? ''));
    if ($result['ok']) {
        $user = Auth::user();
        redirect($user['role'] === 'admin' ? '/admin/index.php' : '/dashboard.php');
    }
    $error = $result['error'];
}

$pageTitle = 'Вход';
require __DIR__ . '/../templates/header.php';
?>

<section class="card form-narrow">
  <h2>Вход</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= Csrf::field() ?>
    <div class="field">
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required autofocus>
    </div>
    <div class="field">
      <label for="password">Пароль</label>
      <input id="password" name="password" type="password" required>
    </div>
    <button type="submit" class="btn btn-accent">Войти</button>
  </form>
  <p style="margin-top:16px; font-size:14px;">Нет аккаунта? <a href="/register.php" style="color:var(--accent);">Зарегистрироваться</a></p>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
