<?php
/**
 * helpers.php — мелкие утилитарные функции, используемые во всех шаблонах.
 */

/** Безопасный вывод строки в HTML (защита от XSS). */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Редирект с немедленным завершением скрипта. */
function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

/** Положить одноразовое сообщение в сессию (flash-сообщение). */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** Забрать и очистить все flash-сообщения. */
function takeFlashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/** IP-адрес клиента (с учётом возможного прокси/балансировщика). */
function clientIp(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}
