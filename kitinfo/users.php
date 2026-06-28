<?php
require __DIR__ . '/_guard.php';

$users = Database::pdo()->query(
    'SELECT u.*, o.name AS org_name FROM users u
     LEFT JOIN organizations o ON o.id = u.organization_id
     ORDER BY u.created_at DESC'
)->fetchAll();

$pageTitle = 'Пользователи — админ-панель';
require __DIR__ . '/../../templates/header.php';
?>

<div class="admin-shell">
  <?php adminSidebar('users'); ?>
  <div>
    <h2>Пользователи</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Имя</th><th>E-mail</th><th>Организация</th><th>Роль</th><th>Статус</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= e($u['full_name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['org_name'] ?? '—') ?></td>
            <td><span class="badge badge-<?= $u['role'] === 'admin' ? 'admin' : 'user' ?>"><?= e($u['role']) ?></span></td>
            <td><?= $u['is_active'] ? 'активен' : 'заблокирован' ?></td>
            <td>
              <?php if ($u['id'] !== $admin['id']): ?>
                <form method="post" action="/admin/user_edit.php" style="display:flex; gap:8px;">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <select name="role" style="width:auto; padding:6px 10px;">
                    <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>user</option>
                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                  </select>
                  <select name="is_active" style="width:auto; padding:6px 10px;">
                    <option value="1" <?= $u['is_active'] ? 'selected' : '' ?>>активен</option>
                    <option value="0" <?= !$u['is_active'] ? 'selected' : '' ?>>блокирован</option>
                  </select>
                  <button type="submit" class="btn btn-ghost btn-sm">Сохранить</button>
                </form>
              <?php else: ?>
                <span style="color:var(--text-faint); font-size:12.5px;">это вы</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
