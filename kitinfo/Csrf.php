<?php
/**
 * Csrf.php — защита форм от межсайтовой подделки запросов (CSRF).
 * Токен генерируется на сессию и проверяется при каждом POST-запросе.
 */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function check(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    /** Прерывает запрос, если токен неверный. */
    public static function require(): void
    {
        if (!self::check($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            die('Сессия устарела, обновите страницу и попробуйте снова.');
        }
    }
}
