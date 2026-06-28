<?php
/**
 * bootstrap.php — единая точка инициализации: подключает все классы,
 * запускает сессию. Подключается первой строкой в каждом public/*.php.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Csrf.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/Auth.php';

Auth::bootstrap();
