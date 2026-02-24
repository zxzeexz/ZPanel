<?php
/**
 * functions.php
 * ZPanel helper functions
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */

// -----------------------------
// HTML Escape Helper
// -----------------------------
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// -----------------------------
// Redirect Helper
// -----------------------------
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// -----------------------------
// Get User IP
// -----------------------------
function getUserIp(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// -----------------------------
// Database Helpers (PDO)
// -----------------------------

// Fetch single row
function db_fetch(string $sql, array $params = []): ?array {
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row !== false ? $row : null;
}

// Fetch all rows
function db_fetch_all(string $sql, array $params = []): array {
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Execute (insert/update/delete)
function db_execute(string $sql, array $params = []): bool {
    global $db;
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

// -----------------------------
// Password Hashing
// -----------------------------
function hashPassword(string $password, string $method = 'md5'): string {
    switch ($method) {
        case 'plain':
            return $password;
        case 'md5':
        default:
            return md5($password);
    }
}

// Class Job names (Thank you Astral :3)
function get_job_name($class_id) {
    $jobs = [
        0 => "Novice",
        1 => "Swordman",
        2 => "Mage",
        3 => "Archer",
        4 => "Acolyte",
        5 => "Merchant",
        6 => "Thief",
        7 => "Knight",
        8 => "Priest",
        9 => "Wizard",
        10 => "Blacksmith",
        11 => "Hunter",
        12 => "Assassin",
        14 => "Crusader",
        15 => "Monk",
        16 => "Sage",
        17 => "Rogue",
        18 => "Alchemist",
        19 => "Bard",
        20 => "Dancer",
        23 => "Super Novice",
        24 => "Gunslinger",
        25 => "Ninja",
		4046 => "Taekwon",
        4047 => "Star Gladiator",
        4049 => "Soul Linker",
        4001 => "High Novice",
        4002 => "High Swordman",
        4003 => "High Mage",
        4004 => "High Archer",
        4005 => "High Acolyte",
        4006 => "High Merchant",
        4007 => "High Thief",
        4008 => "Lord Knight",
        4009 => "High Priest",
        4010 => "High Wizard",
        4011 => "Whitesmith",
        4012 => "Sniper",
        4013 => "Assassin Cross",
        4014 => "Paladin",
        4015 => "Champion",
        4016 => "Professor",
        4017 => "Stalker",
        4018 => "Creator",
        4019 => "Clown",
        4020 => "Gypsy",
        4021 => "Baby",
        4022 => "Baby Swordman",
        4023 => "Baby Mage",
        4024 => "Baby Archer",
        4025 => "Baby Acolyte",
        4026 => "Baby Merchant",
        4027 => "Baby Thief",
        4028 => "Baby Knight",
        4029 => "Baby Priest",
        4030 => "Baby Wizard",
        4031 => "Baby Blacksmith",
        4032 => "Baby Hunter",
        4033 => "Baby Assassin",
        4034 => "Baby Crusader",
        4035 => "Baby Monk",
        4036 => "Baby Sage",
        4037 => "Baby Rogue",
        4038 => "Baby Alchemist",
        4039 => "Baby Bard",
        4040 => "Baby Dancer",
        4041 => "Super Baby",
        4042 => "Taekwon Master",
        4043 => "Star Gladiator (Fusion)",
        4044 => "Soul Reaper",
        4045 => "Baby Gunslinger",
        4046 => "Baby Ninja",
        4047 => "Baby Taekwon",
        4048 => "Baby Star Gladiator",
        4049 => "Baby Soul Linker"
    ];
    
    return $jobs[$class_id] ?? "Unknown (ID: $class_id)";
}

/**
 * Renders the Cloudflare Turnstile widget in a form (client-side).
 * Call this inside <form> where you want the captcha (e.g., before submit button).
 * Automatically loads the script if enabled.
 */
function turnstile_render(): void
{
    global $config;
    if (!($config['turnstile']['enabled'] ?? false)) {
        return;
    }

    $siteKey = $config['turnstile']['site_key'] ?? '';
    if (empty($siteKey)) {
        return;  // No key — skip silently
    }

    // Load script (async/defer to not block)
    echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';

    // Render widget div (implicit mode — auto executes on load)
    echo '<div class="cf-turnstile" data-sitekey="' . e($siteKey) . '"></div>';
}

/**
 * Validates the Cloudflare Turnstile token on form submit (server-side).
 * Call this at the top of POST handling in modules (e.g., login.php, pwreset.php).
 * Returns true if valid, false if invalid or disabled.
 * On false, set $error = $config['msg']['turnstile_invalid'] or similar.
 */
function turnstile_validate(): bool
{
    global $config;
    if (!($config['turnstile']['enabled'] ?? false)) {
        return true;  // Disabled — auto-pass
    }

    $secretKey = $config['turnstile']['secret_key'] ?? '';
    if (empty($secretKey)) {
        return false;  // No secret — fail
    }

    $token = $_POST['cf-turnstile-response'] ?? '';
    if (empty($token)) {
        // NEW: Specific check for missing token
        // In your module, you can now check for this and use 'turnstile_missing' msg
        return false;  // Missing token — fail
    }

    $remoteIp = $_SERVER['REMOTE_ADDR'] ?? '';  // Optional

    // POST to validation endpoint
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data = [
        'secret'   => $secretKey,
        'response' => $token,
        'remoteip' => $remoteIp,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return false;  // API error
    }

    $result = json_decode($response, true);
    return ($result['success'] ?? false) === true;
}