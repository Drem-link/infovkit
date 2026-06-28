<?php
require_once __DIR__ . '/../src/bootstrap.php';

if (Auth::user()) {
    redirect('/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::require();

    $validator = (new Validator())
        ->required($_POST, 'full_name', 'Имя')
        ->required($_POST, 'email', 'E-mail')
        ->email($_POST)
        ->minLength($_POST, 'password', 8, 'Пароль')
        ->phone($_POST);

    if ($validator->fails()) {
        $errors = $validator->errors();
    } else {
        $result = Auth::register(
            trim($_POST['full_name']),
            trim($_POST['email']),
            trim($_POST['phone'] ?? ''),
            $_POST['password']
        );
        if ($result['ok']) {
            flash('success', 'Регистрация прошла успешно.');
            redirect('/dashboard.php');
        } else {
            $errors['email'] = $result['error'];
        }
    }
}

$pageTitle = 'Регистрация';
require __DIR__ . '/../templates/header.php';
?>

<section class="card form-narrow">
  <h2>Регистрация</h2>
  <form method="post">
    <?= Csrf::field() ?>
    <div class="field">
      <label for="full_name">Имя</label>
      <input id="full_name" name="full_name" type="text" required value="<?= e($_POST['full_name'] ?? '') ?>">
      <?php if (!empty($errors['full_name'])): ?><span class="field-error"><?= e($errors['full_name']) ?></span><?php endif; ?>
    </div>
    <div class="field">
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
      <?php if (!empty($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
    </div>
    <div class="field">
      <label for="phone">Телефон</label>
      <input id="phone" name="phone" type="tel" value="<?= e($_POST['phone'] ?? '') ?>">
    </div>
    <div class="field">
      <label for="password">Пароль (не менее 8 символов)</label>
      <input id="password" name="password" type="password" required>
      <?php if (!empty($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
    </div>
    <button type="submit" class="btn btn-accent">Зарегистрироваться</button>
  </form>
  <p style="margin-top:16px; font-size:14px;">Уже есть аккаунт? <a href="/login.php" style="color:var(--accent);">Войти</a></p>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
