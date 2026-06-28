<?php
/** @var string $pageTitle задаётся в каждой странице перед include */
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? APP_NAME) ?> — kitinfo.ru</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="bg-grid"></div>

<header id="site-header">
  <div class="wrap header-row">
    <a href="/index.php" class="logo">kitinfo<span class="dim">.ru</span><span class="cursor"></span></a>

    <nav class="main-nav" id="main-nav">
      <a href="/services.php">Услуги</a>
      <a href="/about.php">О компании</a>
      <a href="/contacts.php">Контакты</a>
      <?php if ($currentUser): ?>
        <a href="/dashboard.php">Личный кабинет</a>
        <?php if ($currentUser['role'] === 'admin'): ?>
          <a href="/admin/index.php">Админ-панель</a>
        <?php endif; ?>
        <a href="/logout.php">Выйти</a>
      <?php else: ?>
        <a href="/login.php">Войти</a>
      <?php endif; ?>
    </nav>

    <div class="header-cta">
      <?php if (!$currentUser): ?>
        <a class="btn btn-ghost" href="/login.php">Войти</a>
      <?php endif; ?>
      <a class="btn btn-accent" href="/contacts.php#request">Оставить заявку</a>
      <button class="burger" id="burger" aria-label="Открыть меню" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<main>
<div class="wrap page-wrap">
<?php foreach (takeFlashes() as $f): ?>
  <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endforeach; ?>
