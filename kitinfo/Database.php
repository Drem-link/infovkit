<?php
/**
 * Database.php — единая точка подключения к MySQL через PDO.
 * Все запросы в проекте идут только через подготовленные выражения (prepared
 * statements) — это и есть основная защита от SQL-инъекций.
 */
class Database
{
    private static ?PDO $instance = null;

    public static function pdo(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false, // настоящие prepared statements
                ]);
            } catch (PDOException $e) {
                error_log('DB connection failed: ' . $e->getMessage());
                http_response_code(500);
                die('Сервис временно недоступен. Попробуйте позже.');
            }
        }

        return self::$instance;
    }
}
