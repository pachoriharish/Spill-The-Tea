<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

if (!isset($_SESSION['username'])) {
    $_SESSION['auth_error'] = 'Log in to like or dislike memes.';
    $_SESSION['auth_tab']   = 'login';
    header('Location: index.php');
    exit;
}

$id    = $_POST['meme_id'] ?? '';
$type  = $_POST['value'] ?? '';
$cat   = $_POST['category'] ?? 'All';
if (!in_array($cat, CATEGORIES, true)) $cat = 'All';
if (!in_array($type, ['like', 'dislike'], true)) { header('Location: index.php'); exit; }
$value = $type === 'like' ? 1 : -1;

$chk = $pdo->prepare('SELECT 1 FROM memes WHERE id = ?');
$chk->execute([$id]);
if (!$chk->fetch()) { header('Location: index.php'); exit; }

$cur = $pdo->prepare('SELECT value FROM reactions WHERE meme_id = ? AND username = ?');
$cur->execute([$id, $_SESSION['username']]);
$existing = $cur->fetchColumn();

if ($existing !== false && (int)$existing === $value) {
    // Same button again = take the reaction back
    $pdo->prepare('DELETE FROM reactions WHERE meme_id = ? AND username = ?')->execute([$id, $_SESSION['username']]);
} elseif ($existing !== false) {
    $pdo->prepare('UPDATE reactions SET value = ? WHERE meme_id = ? AND username = ?')->execute([$value, $id, $_SESSION['username']]);
} else {
    $pdo->prepare('INSERT INTO reactions (meme_id, username, value) VALUES (?, ?, ?)')->execute([$id, $_SESSION['username'], $value]);
}

// Go back to the same meme popup
header('Location: index.php?category=' . urlencode($cat) . '#meme-' . urlencode($id));
