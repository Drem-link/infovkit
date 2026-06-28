<?php
/**
 * Logger.php — пишет события в таблицу logs (просматривается в админ-панели).
 */
class Logger
{
    public static function log(?int $userId, string $action, ?string $details = null): void
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO logs (user_id, action, details, ip_address, user_agent, created_at)
             VALUES (:user_id, :action, :details, :ip, :ua, NOW())'
        );
        $stmt->execute([
            'user_id' => $userId,
            'action'  => $action,
            'details' => $details,
            'ip'      => clientIp(),
            'ua'      => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    }
}
