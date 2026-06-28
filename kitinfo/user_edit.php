<?php
require __DIR__ . '/_guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/users.php');
}
Csrf::require();

$id = (int)($_POST['id'] ?? 0);

// Запрет на самоблокировку/понижение последнего администратора через эту форму
if ($id === $admin['id']) {
    flash('error', 'Нельзя изменить собственную роль здесь.');
    redirect('/admin/users.php');
}

$role = in_array($_POST['role'] ?? '', ['user', 'admin'], true) ? $_POST['role'] : 'user';
$isActive = ($_POST['is_active'] ?? '1') === '1' ? 1 : 0;

Database::pdo()->prepare('UPDATE users SET role = :role, is_active = :active WHERE id = :id')
    ->execute(['role' => $role, 'active' => $isActive, 'id' => $id]);

Logger::log($admin['id'], 'user_updated', "id=$id role=$role active=$isActive");

flash('success', 'Пользователь обновлён.');
redirect('/admin/users.php');
