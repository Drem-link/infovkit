<?php
require_once __DIR__ . '/../src/bootstrap.php';
$pageTitle = 'О компании';
require __DIR__ . '/../templates/header.php';
?>

<section class="card" style="max-width:760px;">
  <div class="eyebrow">О компании</div>
  <h2>kitinfo.ru</h2>
  <p style="margin-bottom:14px;">
    Обслуживаем 1С, серверы и кассовое оборудование малого и среднего бизнеса.
    Работаем напрямую со специалистом — без колл-центра и долгих согласований.
  </p>
  <p>
    Заключаем договор, фиксируем сроки и закрывающие документы.
    Реквизиты и условия сотрудничества — на странице
    <a href="/contacts.php" style="color:var(--accent);">контактов</a>.
  </p>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
