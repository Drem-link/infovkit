<?php
require_once __DIR__ . '/../src/bootstrap.php';

$user = Auth::requireUser();

$stmt = Database::pdo()->prepare(
    'SELECT r.*, s.title AS service_title
     FROM requests r
     LEFT JOIN services s ON s.id = r.service_id
     WHERE r.user_id = :uid
     ORDER BY r.created_at DESC'
);
$stmt->execute(['uid' => $user['id']]);
$requests = $stmt->fetchAll();

$statusLabels = ['new' => 'Новая', 'in_progress' => 'В работе', 'done' => 'Выполнена', 'rejected' => 'Отклонена'];

$pageTitle = 'Личный кабинет';
require __DIR__ . '/../templates/header.php';
?>

<section>
  <div class="eyebrow">Личный кабинет</div>
  <h2>Здравствуйте, <?= e($user['full_name']) ?></h2>
  <p style="margin-bottom:24px;">Ваши заявки:</p>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Дата</th><th>Услуга</th><th>Комментарий</th><th>Статус</th></tr></thead>
      <tbody>
        <?php if (!$requests): ?>
          <tr><td colspan="4">Заявок пока нет. <a href="/contacts.php#request" style="color:var(--accent);">Оставить заявку</a></td></tr>
        <?php endif; ?>
        <?php foreach ($requests as $r): ?>
          <tr>
            <td><?= e(date('d.m.Y H:i', strtotime($r['created_at']))) ?></td>
            <td><?= e($r['service_title'] ?? 'Консультация') ?></td>
            <td><?= e(mb_strimwidth((string)$r['message'], 0, 60, '…')) ?></td>
            <td><span class="badge badge-<?= e($r['status']) ?>"><?= e($statusLabels[$r['status']]) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
