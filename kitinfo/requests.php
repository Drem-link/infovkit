<?php
require __DIR__ . '/_guard.php';

$filter = $_GET['status'] ?? '';
$allowed = ['new', 'in_progress', 'done', 'rejected'];
$sql = 'SELECT r.*, s.title AS service_title, u.full_name AS user_name
        FROM requests r
        LEFT JOIN services s ON s.id = r.service_id
        LEFT JOIN users u ON u.id = r.user_id';
$params = [];
if (in_array($filter, $allowed, true)) {
    $sql .= ' WHERE r.status = :status';
    $params['status'] = $filter;
}
$sql .= ' ORDER BY r.created_at DESC';

$stmt = Database::pdo()->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

$statusLabels = ['new' => 'Новая', 'in_progress' => 'В работе', 'done' => 'Выполнена', 'rejected' => 'Отклонена'];

$pageTitle = 'Заявки — админ-панель';
require __DIR__ . '/../../templates/header.php';
?>

<div class="admin-shell">
  <?php adminSidebar('requests'); ?>
  <div>
    <h2>Заявки</h2>

    <div style="display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap;">
      <a class="btn btn-sm <?= $filter === '' ? 'btn-accent' : 'btn-ghost' ?>" href="/admin/requests.php">Все</a>
      <?php foreach ($statusLabels as $key => $label): ?>
        <a class="btn btn-sm <?= $filter === $key ? 'btn-accent' : 'btn-ghost' ?>" href="/admin/requests.php?status=<?= $key ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr><th>Дата</th><th>Клиент</th><th>Услуга</th><th>Комментарий</th><th>Статус</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($requests as $r): ?>
          <tr>
            <td><?= e(date('d.m.Y H:i', strtotime($r['created_at']))) ?></td>
            <td>
              <?= e($r['user_name'] ?? $r['guest_name'] ?? 'Гость') ?><br>
              <span style="color:var(--text-faint); font-size:12px;"><?= e($r['guest_phone'] ?? '') ?></span>
            </td>
            <td><?= e($r['service_title'] ?? 'Консультация') ?></td>
            <td><?= e(mb_strimwidth((string)$r['message'], 0, 50, '…')) ?></td>
            <td><span class="badge badge-<?= e($r['status']) ?>"><?= e($statusLabels[$r['status']]) ?></span></td>
            <td>
              <form method="post" action="/admin/request_status.php" style="display:flex; gap:8px;">
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <select name="status" style="width:auto; padding:6px 10px;">
                  <?php foreach ($statusLabels as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $r['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-ghost btn-sm">Сохранить</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
