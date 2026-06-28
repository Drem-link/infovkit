<?php
require __DIR__ . '/_guard.php';

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$service = ['code' => '', 'title' => '', 'category' => '1c', 'description' => '', 'price_from' => '', 'is_active' => 1, 'sort_order' => 0];
$errors = [];

if ($id) {
    $stmt = Database::pdo()->prepare('SELECT * FROM services WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();
    if (!$found) { http_response_code(404); die('Услуга не найдена'); }
    $service = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::require();

    $validator = (new Validator())
        ->required($_POST, 'code', 'Код')
        ->required($_POST, 'title', 'Название');

    if ($validator->fails()) {
        $errors = $validator->errors();
        $service = array_merge($service, $_POST);
    } else {
        $params = [
            'code'        => trim($_POST['code']),
            'title'       => trim($_POST['title']),
            'category'    => $_POST['category'],
            'description' => trim($_POST['description'] ?? ''),
            'price_from'  => $_POST['price_from'] !== '' ? (float)$_POST['price_from'] : null,
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        ];

        if ($id) {
            $params['id'] = $id;
            Database::pdo()->prepare(
                'UPDATE services SET code=:code, title=:title, category=:category, description=:description,
                 price_from=:price_from, is_active=:is_active, sort_order=:sort_order WHERE id=:id'
            )->execute($params);
            Logger::log($admin['id'], 'service_updated', 'id=' . $id);
        } else {
            Database::pdo()->prepare(
                'INSERT INTO services (code, title, category, description, price_from, is_active, sort_order)
                 VALUES (:code, :title, :category, :description, :price_from, :is_active, :sort_order)'
            )->execute($params);
            Logger::log($admin['id'], 'service_created', $params['code']);
        }

        flash('success', 'Услуга сохранена.');
        redirect('/admin/services.php');
    }
}

$pageTitle = ($id ? 'Изменить' : 'Новая') . ' услуга';
require __DIR__ . '/../../templates/header.php';
?>

<div class="admin-shell">
  <?php adminSidebar('services'); ?>
  <div>
    <h2><?= $id ? 'Изменить услугу' : 'Новая услуга' ?></h2>
    <form method="post" class="card form-narrow">
      <?= Csrf::field() ?>
      <div class="field">
        <label for="code">Код</label>
        <input id="code" name="code" value="<?= e($service['code']) ?>" required>
        <?php if (!empty($errors['code'])): ?><span class="field-error"><?= e($errors['code']) ?></span><?php endif; ?>
      </div>
      <div class="field">
        <label for="title">Название</label>
        <input id="title" name="title" value="<?= e($service['title']) ?>" required>
        <?php if (!empty($errors['title'])): ?><span class="field-error"><?= e($errors['title']) ?></span><?php endif; ?>
      </div>
      <div class="field">
        <label for="category">Категория</label>
        <select id="category" name="category">
          <?php foreach (['1c' => '1С', 'outsource' => 'Аутсорсинг', 'server' => 'Серверы', 'kkm' => 'ККТ'] as $val => $label): ?>
            <option value="<?= $val ?>" <?= $service['category'] === $val ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="description">Описание</label>
        <textarea id="description" name="description"><?= e($service['description']) ?></textarea>
      </div>
      <div class="field">
        <label for="price_from">Цена от (₽)</label>
        <input id="price_from" name="price_from" type="number" step="1" min="0" value="<?= e((string)$service['price_from']) ?>">
      </div>
      <div class="field">
        <label for="sort_order">Порядок сортировки</label>
        <input id="sort_order" name="sort_order" type="number" value="<?= e((string)$service['sort_order']) ?>">
      </div>
      <div class="field" style="flex-direction:row; align-items:center; gap:10px;">
        <input id="is_active" name="is_active" type="checkbox" style="width:auto;" <?= $service['is_active'] ? 'checked' : '' ?>>
        <label for="is_active" style="text-transform:none; font-size:14px;">Услуга активна (показывается на сайте)</label>
      </div>
      <button type="submit" class="btn btn-accent">Сохранить</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
