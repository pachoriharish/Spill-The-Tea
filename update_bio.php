<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$bio = mb_substr(trim($_POST['bio'] ?? ''), 0, 500);
$pdo->prepare('UPDATE users SET bio = ? WHERE username = ?')->execute([$bio, $_SESSION['username']]);

$_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Bio saved.'];
header('Location: profile.php');
