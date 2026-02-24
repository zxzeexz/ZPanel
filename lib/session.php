<?php
/**
 * lib/session.php
 * ZPanel session handler
 * Revision 5 [updated for reliable abandoned cleanup]
 * Zee ^_~
 */
class Session
{
    private static string $last_error = '';

    public static function getLastError(): string
    {
        return self::$last_error;
    }

    // Global cleanup for expired active sessions (callable from init.php)
    public static function cleanupExpired(): void
    {
        $sqlCleanup = "UPDATE cp_sessions SET isActive = 0 WHERE expires_at < :now AND isActive = 1";
        db_execute($sqlCleanup, [':now' => time()]);
    }

    // Check if user is logged in
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['account_id']) && self::validateSession($_SESSION['session_id'] ?? '');
    }

    // Create a new session in cp_sessions
    public static function create(int $accountId, string $username): bool
    {
	global $config; //pull config
        self::cleanupExpired();  // Cleanup before new
        
        $sessionId = bin2hex(random_bytes(32));
        $ip        = getUserIp();
        $lifetime  = $config['security']['max_logintime'];
        $expiry    = time() + $lifetime;

        // Save in DB with isActive=1
        $sql = "INSERT INTO cp_sessions (session_id, account_id, ip_address, expires_at, isActive) 
                VALUES (:sid, :aid, :ip, :exp, 1)";
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
	global $config; //pull config
        self::$last_error = ''; // Reset error

        if (empty($sessionId)) {
            return false;
        }

        // Select the session first (before cleanup)
        $sql = "SELECT * FROM cp_sessions WHERE session_id = :sid LIMIT 1";
        $row = db_fetch($sql, [':sid' => $sessionId]);

        if (!$row) {
            self::$last_error = 'invalid';
            return false;
        }

        if ($row['isActive'] == 0) {
            self::$last_error = 'invalid';
            return false;
        }

        $expired = $row['expires_at'] < time();
	
        if ($expired) {
            self::$last_error = 'expired';
        }

        // Now cleanup all expired active (including this if expired)
        self::cleanupExpired();

        if ($expired) {
            self::destroy();
            return false;
        }
	
        // Sliding expiry: Extend if valid
        $lifetime = $config['security']['max_logintime'];
        $newExpiry = time() + $lifetime;
        $sqlUpdate = "UPDATE cp_sessions SET expires_at = :newexp WHERE session_id = :sid AND isActive = 1";
        db_execute($sqlUpdate, [':newexp' => $newExpiry, ':sid' => $sessionId]);

        return true;
    }

    // Destroy session (logout)
    public static function destroy(): void
    {
        if (!empty($_SESSION['session_id'])) {
            $sql = "UPDATE cp_sessions SET isActive = 0 WHERE session_id = :sid";
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