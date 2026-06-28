<?php
/**
 * Auth.php — регистрация, вход, выход, проверка ролей.
 *
 * Роли в системе:
 *   - guest  — неаутентифицированный посетитель (нет записи в users, доступ
 *              только к публичным разделам и форме заявки)
 *   - user   — зарегистрированный клиент (личный кабинет, свои заявки)
 *   - admin  — администратор (управление контентом, пользователями, логами)
 */
class Auth
{
    /** Запускает сессию с безопасными параметрами (вызывается один раз в bootstrap). */
    public static function bootstrap(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function register(string $name, string $email, string $phone, string $password, ?int $organizationId = null): array
    {
        $pdo = Database::pdo();

        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            return ['ok' => false, 'error' => 'Пользователь с таким e-mail уже зарегистрирован'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT); // bcrypt

        $stmt = $pdo->prepare(
            'INSERT INTO users (organization_id, full_name, email, phone, password_hash, role, is_active)
             VALUES (:org_id, :name, :email, :phone, :hash, "user", 1)'
        );
        $stmt->execute([
            'org_id' => $organizationId,
            'name'   => $name,
            'email'  => $email,
            'phone'  => $phone,
            'hash'   => $hash,
        ]);

        $userId = (int)$pdo->lastInsertId();
        Logger::log($userId, 'register', $email);
        self::loginById($userId);

        return ['ok' => true];
    }

    public static function attemptLogin(string $email, string $password): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            Logger::log(null, 'login_failed', "email not found: $email");
            return ['ok' => false, 'error' => 'Неверный e-mail или пароль'];
        }

        // Блокировка после нескольких неудачных попыток (защита от подбора пароля)
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return ['ok' => false, 'error' => 'Слишком много попыток входа. Попробуйте позже.'];
        }

        if (!$user['is_active']) {
            return ['ok' => false, 'error' => 'Учётная запись отключена. Обратитесь к администратору.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            self::registerFailedAttempt($user);
            Logger::log($user['id'], 'login_failed', 'wrong password');
            return ['ok' => false, 'error' => 'Неверный e-mail или пароль'];
        }

        // Успешный вход — сбрасываем счётчик попыток
        $pdo->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = :id')
            ->execute(['id' => $user['id']]);

        self::loginById((int)$user['id']);
        Logger::log($user['id'], 'login_success', null);

        return ['ok' => true];
    }

    private static function registerFailedAttempt(array $user): void
    {
        $attempts = (int)$user['failed_attempts'] + 1;
        $lockUntil = null;
        if ($attempts >= LOGIN_MAX_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', time() + LOGIN_LOCK_MINUTES * 60);
        }
        Database::pdo()->prepare(
            'UPDATE users SET failed_attempts = :a, locked_until = :l WHERE id = :id'
        )->execute(['a' => $attempts, 'l' => $lockUntil, 'id' => $user['id']]);
    }

    private static function loginById(int $userId): void
    {
        // Регенерация ID сессии при входе — защита от session fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        $userId = self::user()['id'] ?? null;
        Logger::log($userId, 'logout', null);
        $_SESSION = [];
        session_destroy();
    }

    /** Текущий пользователь (или null для гостя). Кэшируется на запрос. */
    public static function user(): ?array
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached === false ? null : $cached;
        }
        if (empty($_SESSION['user_id'])) {
            $cached = false;
            return null;
        }
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE id = :id AND is_active = 1');
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();
        $cached = $user ?: false;
        return $user ?: null;
    }

    public static function isGuest(): bool { return self::user() === null; }
    public static function isAdmin(): bool { return (self::user()['role'] ?? null) === 'admin'; }

    /** Требует авторизации (любая роль user/admin). Иначе редирект на /login.php. */
    public static function requireUser(): array
    {
        $user = self::user();
        if (!$user) {
            redirect('/login.php');
        }
        return $user;
    }

    /** Требует роль admin. Иначе 403. */
    public static function requireAdmin(): array
    {
        $user = self::requireUser();
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Доступ запрещён: требуются права администратора.');
        }
        return $user;
    }
}
