<?php
require __DIR__ . '/_guard.php';

$services = Database::pdo()->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();

$pageTitle = 'Услуги — админ-панель';
require __DIR__ . '/../../templates/header.php';
?>

<div class="admin-shell">
  <?php adminSidebar('services'); ?>
  <div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
      <h2 style="margin:0;">Услуги</h2>
      <a class="btn btn-accent btn-sm" href="/admin/service_edit.php">+ Добавить услугу</a>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr><th>Код</th><th>Название</th><th>Категория</th><th>Цена от</th><th>Статус</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td><?= e($s['code']) ?></td>
            <td><?= e($s['title']) ?></td>
            <td><?= e($s['category']) ?></td>
            <td><?= $s['price_from'] !== null ? number_format((float)$s['price_from'],0,'',' ').' ₽' : '—' ?></td>
            <td><?= $s['is_active'] ? '<span class="badge badge-done">активна</span>' : '<span class="badge badge-rejected">скрыта</span>' ?></td>
            <td style="display:flex; gap:8px;">
              <a class="btn btn-ghost btn-sm" href="/admin/service_edit.php?id=<?= (int)$s['id'] ?>">Изменить</a>
              <form method="post" action="/admin/service_delete.php" data-confirm="Удалить услугу «<?= e($s['title']) ?>»?">
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
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
