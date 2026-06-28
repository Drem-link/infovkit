<?php
require_once __DIR__ . '/../src/bootstrap.php';

$services = Database::pdo()->query('SELECT id, title FROM services WHERE is_active = 1 ORDER BY sort_order')->fetchAll();
$currentUser = Auth::user();
$preselect = (int)($_GET['service'] ?? 0);

$pageTitle = 'Контакты';
require __DIR__ . '/../templates/header.php';
?>

<section class="grid-2">
  <div class="card">
    <div class="eyebrow">Контакты</div>
    <h2>Как с нами связаться</h2>
    <p>Телефон: <b style="color:var(--text);">+7 (___) ___-__-__</b></p>
    <p>E-mail: <b style="color:var(--text);">info@kitinfo.ru</b></p>
    <p>Режим работы: Пн–Пт, 9:00–18:00</p>
  </div>

  <div class="card" id="request">
    <div class="eyebrow">Заявка</div>
    <h2>Оставить заявку</h2>

    <form method="post" action="/request.php" class="form-narrow">
      <?= Csrf::field() ?>

      <?php if (!$currentUser): ?>
        <div class="field">
          <label for="guest_name">Имя</label>
          <input id="guest_name" name="guest_name" type="text" required>
        </div>
        <div class="field">
          <label for="guest_phone">Телефон</label>
          <input id="guest_phone" name="guest_phone" type="tel" required>
        </div>
        <div class="field">
          <label for="guest_email">E-mail (необязательно)</label>
          <input id="guest_email" name="guest_email" type="email">
        </div>
      <?php else: ?>
        <p style="font-size:13.5px;">Заявка будет привязана к вашему аккаунту: <b style="color:var(--text);"><?= e($currentUser['email']) ?></b></p>
      <?php endif; ?>

      <div class="field">
        <label for="service_id">Услуга</label>
        <select id="service_id" name="service_id">
          <option value="">Не знаю — нужна консультация</option>
          <?php foreach ($services as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= $preselect === (int)$s['id'] ? 'selected' : '' ?>>
              <?= e($s['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label for="message">Комментарий</label>
        <textarea id="message" name="message" placeholder="Опишите задачу в двух словах"></textarea>
      </div>

      <button type="submit" class="btn btn-accent">Отправить заявку</button>
      <p style="font-size:12.5px; color:var(--text-faint);">Нажимая кнопку, вы соглашаетесь на обработку персональных данных.</p>
    </form>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
