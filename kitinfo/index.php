<?php
require __DIR__ . '/_guard.php';

$pdo = Database::pdo();
$stats = [
    'users'        => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'services'     => $pdo->query('SELECT COUNT(*) FROM services WHERE is_active = 1')->fetchColumn(),
    'requests_new' => $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'new'")->fetchColumn(),
    'requests_all' => $pdo->query('SELECT COUNT(*) FROM requests')->fetchColumn(),
];

$pageTitle = 'Админ-панель';
require __DIR__ . '/../../templates/header.php';
?>

<div class="admin-shell">
  <?php adminSidebar('index'); ?>
  <div>
    <h2>Дэшборд</h2>
    <div class="stat-grid">
      <div class="stat-card"><div class="num"><?= (int)$stats['users'] ?></div><div class="label">Пользователей</div></div>
      <div class="stat-card"><div class="num"><?= (int)$stats['services'] ?></div><div class="label">Активных услуг</div></div>
      <div class="stat-card"><div class="num"><?= (int)$stats['requests_new'] ?></div><div class="label">Новых заявок</div></div>
      <div class="stat-card"><div class="num"><?= (int)$stats['requests_all'] ?></div><div class="label">Всего заявок</div></div>
    </div>
    <p>Вы вошли как <b style="color:var(--text);"><?= e($admin['full_name']) ?></b> (роль: admin).</p>
  </div>
</div>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
