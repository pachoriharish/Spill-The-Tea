<?php
session_start();
require 'db.php';

function fail($msg) {
    $_SESSION['auth_error'] = $msg;
    $_SESSION['auth_tab']   = 'signup';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$display  = trim($_POST['display_name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($display === '' || mb_strlen($display) > 100) fail('Enter a display name (up to 100 characters).');
if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) fail('Username must be 3–30 letters, numbers or underscores.');
if (strlen($password) < 6) fail('Password must be at least 6 characters.');
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) fail('That email address is not valid.');

$check = $pdo->prepare('SELECT 1 FROM users WHERE username = ?');
$check->execute([$username]);
if ($check->fetch()) fail('That username is taken. Try another one.');

$ins = $pdo->prepare('INSERT INTO users (username, password_hash, display_name, email, bio, joined) VALUES (?, ?, ?, ?, ?, ?)');
$ins->execute([$username, password_hash($password, PASSWORD_DEFAULT), $display, $email !== '' ? $email : null, '', time()]);

session_regenerate_id(true);
$_SESSION['username'] = $username;
header('Location: profile.php');
