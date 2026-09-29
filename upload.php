<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

function back($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: profile.php');
    exit;
}

$allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
$category = $_POST['category'] ?? '';
$caption  = mb_substr(trim($_POST['caption'] ?? ''), 0, 300);

if (!in_array($category, CATEGORIES, true)) back('error', 'Choose a valid category.');
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) back('error', 'The upload failed. Choose an image and try again.');
if ($_FILES['image']['size'] > 5 * 1024 * 1024) back('error', 'Image is too large. The limit is 5 MB.');

// Check the real file type, not the name the browser sent
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']);
if (!isset($allowed[$mime])) back('error', 'Only JPG, PNG, GIF and WebP images are allowed.');

$filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
if (!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $filename)) back('error', 'Could not save the image. Check that the uploads folder is writable.');

$u = $pdo->prepare('SELECT display_name FROM users WHERE username = ?');
$u->execute([$_SESSION['username']]);
$displayName = $u->fetchColumn();

$pdo->prepare('INSERT INTO memes (id, uploader, uploader_name, image_path, caption, category, ts) VALUES (?, ?, ?, ?, ?, ?, ?)')
    ->execute([uuid(), $_SESSION['username'], $displayName, $filename, $caption, $category, time()]);

back('ok', 'Meme uploaded.');
