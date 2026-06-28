<?php
/**
 * _guard.php — подключается первой строкой в каждом public/admin/*.php.
 * Проверяет роль admin и выводит общий сайдбар (вызывается через функцию
 * adminSidebar(), чтобы каждая страница сама решала, когда его рисовать).
 */
require_once __DIR__ . '/../../src/bootstrap.php';
$admin = Auth::requireAdmin();

function adminSidebar(string $active): void
{
    $items = [
        'index'    => ['/admin/index.php', 'Дэшборд'],
        'services' => ['/admin/services.php', 'Услуги'],
        'users'    => ['/admin/users.php', 'Пользователи'],
        'requests' => ['/admin/requests.php', 'Заявки'],
        'logs'     => ['/admin/logs.php', 'Журнал действий'],
    ];
    echo '<nav class="admin-side">';
    foreach ($items as $key => [$url, $label]) {
        $cls = $key === $active ? 'active' : '';
        echo '<a class="' . $cls . '" href="' . e($url) . '">' . e($label) . '</a>';
    }
    echo '</nav>';
}
