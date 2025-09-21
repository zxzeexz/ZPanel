<?php
/**
 * lib/session.php
 * ZPanel session handler
 * Revision 2 [9-21-2025]
 * Zee ^_~
 */
class Session
{
    // Check if user is logged in
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['account_id']) && self::validateSession($_SESSION['session_id'] ?? '');
    }

    // Create a new session in cp_sessions
    public static function create(int $accountId, string $username): bool
    {
		// Cleanup expired sessions before creating a new one
		$sqlCleanup = "DELETE FROM cp_sessions WHERE expires_at < :now";
		db_execute($sqlCleanup, [':now' => time()]);
		
        $sessionId = bin2hex(random_bytes(32));
        $ip        = getUserIp();
		$lifetime  = $config['security']['max_logintime'] ?? 3600;
        $expiry    = time() + $lifetime;

        // Save in DB
        $sql = "INSERT INTO cp_sessions (session_id, account_id, ip_address, expires_at) 
                VALUES (:sid, :aid, :ip, :exp)";
        $ok = db_execute($sql, [
            ':sid' => $sessionId,
            ':aid' => $accountId,
            ':ip'  => $ip,
            ':exp' => $expiry,
        ]);

        if ($ok) {
            // Save in PHP session
            $_SESSION['session_id'] = $sessionId;
            $_SESSION['account_id'] = $accountId;   // real login.account_id
            $_SESSION['username']   = $username;    // real login.userid
            return true;
        }

        return false;
    }

    // Validate session against DB
    public static function validateSession(string $sessionId): bool
    {
        if (empty($sessionId)) {
            return false;
        }
		// Cleanup: remove expired sessions
		$sqlCleanup = "DELETE FROM cp_sessions WHERE expires_at < :now";
		db_execute($sqlCleanup, [':now' => time()]);
		
        $sql  = "SELECT * FROM cp_sessions WHERE session_id = :sid LIMIT 1";
        $row  = db_fetch($sql, [':sid' => $sessionId]);

        if (!$row) {
            return false;
        }

        // Expired?
        if ($row['expires_at'] < time()) {
            self::destroy();
            return false;
        }

        return true;
    }

    // Destroy session (logout)
    public static function destroy(): void
    {
        if (!empty($_SESSION['session_id'])) {
            $sql = "DELETE FROM cp_sessions WHERE session_id = :sid";
            db_execute($sql, [':sid' => $_SESSION['session_id']]);
        }

        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    // Get current account ID
    public static function getAccountId(): ?int
    {
        return $_SESSION['account_id'] ?? null;
    }

    // Get any session value
    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }
}
