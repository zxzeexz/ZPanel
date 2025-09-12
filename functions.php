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

// Class Job names (only pre renewal for now)
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
        13 => "Crusader",
        14 => "Monk",
        15 => "Sage",
        16 => "Rogue",
        17 => "Alchemist",
        18 => "Bard",
        19 => "Dancer",
        20 => "Super Novice",
        21 => "Gunslinger",
        22 => "Ninja",
        23 => "Taekwon",
        24 => "Star Gladiator",
        25 => "Soul Linker",
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
