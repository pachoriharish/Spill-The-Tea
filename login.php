<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$row = $stmt->fetch();

if ($row && password_verify($password, $row['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['username'] = $row['username'];
    header('Location: index.php');
    exit;
}

$_SESSION['auth_error'] = 'Wrong username or password.';
$_SESSION['auth_tab']   = 'login';
header('Location: index.php');
