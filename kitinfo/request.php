<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/contacts.php');
}

Csrf::require();

$currentUser = Auth::user();
$serviceId = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
$message   = trim((string)($_POST['message'] ?? ''));

$validator = new Validator();

if ($currentUser) {
    $guestName = $guestPhone = $guestEmail = null;
} else {
    $validator->required($_POST, 'guest_name', 'Имя')
               ->required($_POST, 'guest_phone', 'Телефон')
               ->phone($_POST, 'guest_phone')
               ->email($_POST, 'guest_email');
    $guestName  = trim((string)($_POST['guest_name'] ?? ''));
    $guestPhone = trim((string)($_POST['guest_phone'] ?? ''));
    $guestEmail = trim((string)($_POST['guest_email'] ?? '')) ?: null;
}

if ($validator->fails()) {
    flash('error', 'Проверьте поля формы: ' . implode('; ', $validator->errors()));
    redirect('/contacts.php');
}

$stmt = Database::pdo()->prepare(
    'INSERT INTO requests (user_id, organization_id, service_id, guest_name, guest_phone, guest_email, message, status)
     VALUES (:user_id, :org_id, :service_id, :guest_name, :guest_phone, :guest_email, :message, "new")'
);
$stmt->execute([
    'user_id'     => $currentUser['id'] ?? null,
    'org_id'      => $currentUser['organization_id'] ?? null,
    'service_id'  => $serviceId,
    'guest_name'  => $guestName,
    'guest_phone' => $guestPhone,
    'guest_email' => $guestEmail,
    'message'     => $message,
]);

Logger::log($currentUser['id'] ?? null, 'request_created', 'request_id=' . Database::pdo()->lastInsertId());

flash('success', 'Заявка принята. Свяжемся с вами в ближайшее рабочее время.');
redirect($currentUser ? '/dashboard.php' : '/contacts.php');
