<?php
/**
 * config.php — общая конфигурация приложения.
 * Реальные значения в проде передаются через переменные окружения
 * (см. .env.example), здесь — значения по умолчанию для разработки.
 */

// --- безопасные настройки сессии (выставляются ДО session_start()) ---
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');
// На проде с HTTPS обязательно включить:
// ini_set('session.cookie_secure', '1');

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'kitinfo');
define('DB_USER', getenv('DB_USER') ?: 'kitinfo_app');
define('DB_PASS', getenv('DB_PASS') ?: 'change_me');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'kitinfo.ru');
define('APP_ENV', getenv('APP_ENV') ?: 'production'); // production | development
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCK_MINUTES', 15);

// Вывод ошибок только в режиме разработки — на проде ошибки пишутся в лог,
// а пользователю показывается общая страница (защита от утечки информации).
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
