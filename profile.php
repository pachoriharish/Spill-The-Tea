<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$me = $stmt->fetch();
if (!$me) { session_destroy(); header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT m.*,
  (SELECT COUNT(*) FROM reactions r WHERE r.meme_id = m.id AND r.value = 1)  AS likes,
  (SELECT COUNT(*) FROM reactions r WHERE r.meme_id = m.id AND r.value = -1) AS dislikes
  FROM memes m WHERE m.uploader = ? ORDER BY m.ts DESC");
$stmt->execute([$me['username']]);
$mine = $stmt->fetchAll();
$totalLikes = array_sum(array_column($mine, 'likes'));
$totalDislikes = array_sum(array_column($mine, 'dislikes'));

$pageTitle = 'Profile';
require 'header.php';
?>
<section class="card account">
  <h1><?= e($me['display_name']) ?></h1>
  <p class="muted">@<?= e($me['username']) ?><?= $me['email'] ? ' · ' . e($me['email']) : '' ?></p>
  <p class="muted">Joined <?= date('j M Y', (int)$me['joined']) ?></p>
  <p class="stats totals">Total on your memes: 👍 <?= (int)$totalLikes ?> &nbsp; 👎 <?= (int)$totalDislikes ?></p>
  <form method="POST" action="update_bio.php">
    <label>Bio <textarea name="bio" rows="3" maxlength="500" placeholder="Tell people what kind of tea you spill"><?= e($me['bio']) ?></textarea></label>
    <button type="submit" class="btn">Save bio</button>
  </form>
</section>

<section class="card">
  <h2>Upload a meme</h2>
  <form method="POST" action="upload.php" enctype="multipart/form-data" class="upload-form">
    <label>Image (JPG, PNG, GIF or WebP, up to 5 MB) <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required></label>
    <label>Caption <input type="text" name="caption" maxlength="300" placeholder="Say something about it"></label>
    <label>Category
      <select name="category" required>
        <?php foreach (CATEGORIES as $c): ?><option><?= e($c) ?></option><?php endforeach; ?>
      </select>
    </label>
    <button type="submit" class="btn">Upload</button>
  </form>
</section>

<h2>Your memes</h2>
<?php if (!$mine): ?>
  <p class="empty">You haven't uploaded anything yet. Use the form above to post your first meme.</p>
<?php else: ?>
  <section class="grid">
    <?php foreach ($mine as $m): ?>
      <article class="tile own">
        <span class="badge"><?= e($m['category']) ?></span>
        <img src="uploads/<?= e($m['image_path']) ?>" alt="<?= e($m['caption'] ?: 'Your meme') ?>" loading="lazy">
        <span class="tile-by"><?= e($m['caption']) ?></span>
        <span class="stats">👍 <?= (int)$m['likes'] ?> &nbsp; 👎 <?= (int)$m['dislikes'] ?></span>
        <form method="POST" action="delete.php" onsubmit="return confirm('Delete this meme?');">
          <input type="hidden" name="meme_id" value="<?= e($m['id']) ?>">
          <button type="submit" class="btn btn-delete">Delete</button>
        </form>
      </article>
    <?php endforeach; ?>
  </section>
<?php endif; ?>
<?php require 'footer.php'; ?>
