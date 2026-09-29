<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$id = $_POST['meme_id'] ?? '';

// Only the owner can delete: the uploader must match the logged-in user
$stmt = $pdo->prepare('SELECT image_path FROM memes WHERE id = ? AND uploader = ?');
$stmt->execute([$id, $_SESSION['username']]);
$meme = $stmt->fetch();

if ($meme) {
    $pdo->prepare('DELETE FROM reactions WHERE meme_id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM memes WHERE id = ? AND uploader = ?')->execute([$id, $_SESSION['username']]);
    $file = __DIR__ . '/uploads/' . basename($meme['image_path']);
    if (is_file($file)) unlink($file);
    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Meme deleted.'];
} else {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'That meme could not be found.'];
}
header('Location: profile.php');
