<?php
require __DIR__ . '/_guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/requests.php');
}
Csrf::require();

$id = (int)($_POST['id'] ?? 0);
$allowed = ['new', 'in_progress', 'done', 'rejected'];
$status = in_array($_POST['status'] ?? '', $allowed, true) ? $_POST['status'] : 'new';

Database::pdo()->prepare('UPDATE requests SET status = :status WHERE id = :id')
    ->execute(['status' => $status, 'id' => $id]);

Logger::log($admin['id'], 'request_status_changed', "id=$id status=$status");

flash('success', 'Статус заявки обновлён.');
redirect('/admin/requests.php');
