<?php
require __DIR__ . '/_guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/services.php');
}
Csrf::require();

$id = (int)($_POST['id'] ?? 0);
Database::pdo()->prepare('DELETE FROM services WHERE id = :id')->execute(['id' => $id]);
Logger::log($admin['id'], 'service_deleted', 'id=' . $id);

flash('success', 'Услуга удалена.');
redirect('/admin/services.php');
