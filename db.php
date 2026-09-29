<?php
// Shared database connection + small helpers. Edit the 4 values below.
$dbHost = 'localhost';
$dbName = 'spillthetea';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $ex) {
    die('Could not connect to the database. Check the settings in db.php.');
}

const CATEGORIES = ['Relatable', 'Trending', 'Random', 'Dark Humor'];

// Escape output for HTML
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// UUID v4 for meme ids
function uuid() {
    $d = random_bytes(16);
    $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
    $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}
