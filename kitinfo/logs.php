<?php
require __DIR__ . '/_guard.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 30;
$offset = ($page - 1) * $perPage;
$actionFilter = trim((string)($_GET['action'] ?? ''));

$where = '';
$params = [];
if ($actionFilter !== '') {
    $where = 'WHERE l.action = :action';
    $params['action'] = $actionFilter;
}

$countStmt = Database::pdo()->prepare("SELECT COUNT(*) FROM logs l $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$stmt = Database::pdo()->prepare(
    "SELECT l.*, u.full_name FROM logs l
     LEFT JOIN users u ON u.id = l.user_id
     $where
     ORDER BY l.created_at DESC
     LIMIT :limit OFFSET :offset"
);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $k => $v) { $stmt->bindValue(":$k", $v); }
$stmt->execute();
$logs = $stmt->fetchAll();

$totalPages = max(1, (int)ceil($total / $perPage));

$pageTitle = 'Журнал действий — админ-панель';
require __DIR__ . '/../../templates/header.php';
?>

<div class="admin-shell">
  <?php adminSidebar('logs'); ?>
  <div>
    <h2>Журнал действий</h2>

    <form method="get" style="display:flex; gap:10px; margin-bottom:18px;">
      <select name="action" style="width:auto;">
        <option value="">Все действия</option>
        <?php foreach (['login_success','login_failed','logout','register','request_created','service_created','service_updated','service_deleted','user_updated','request_status_changed'] as $a): ?>
          <option value="<?= $a ?>" <?= $actionFilter === $a ? 'selected' : '' ?>><?= $a ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-ghost btn-sm" type="submit">Фильтровать</button>
    </form>

    <div class="table-wrap">
      <table>
        <thead><tr><th>Дата</th><th>Пользователь</th><th>Действие</th><th>Детали</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $l): ?>
          <tr>
            <td><?= e(date('d.m.Y H:i:s', strtotime($l['created_at']))) ?></td>
            <td><?= e($l['full_name'] ?? '— (гость/система)') ?></td>
            <td><code><?= e($l['action']) ?></code></td>
            <td><?= e((string)$l['details']) ?></td>
            <td><?= e((string)$l['ip_address']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div style="display:flex; gap:10px; margin-top:18px;">
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a class="btn btn-sm <?= $p === $page ? 'btn-accent' : 'btn-ghost' ?>"
           href="?page=<?= $p ?>&action=<?= e($actionFilter) ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
