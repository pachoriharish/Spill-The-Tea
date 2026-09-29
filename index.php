<?php
session_start();
require 'db.php';

$cat = $_GET['category'] ?? 'All';
if ($cat !== 'All' && !in_array($cat, CATEGORIES, true)) $cat = 'All';

$me = $_SESSION['username'] ?? '';
$select = "SELECT m.*,
  (SELECT COUNT(*) FROM reactions r WHERE r.meme_id = m.id AND r.value = 1)  AS likes,
  (SELECT COUNT(*) FROM reactions r WHERE r.meme_id = m.id AND r.value = -1) AS dislikes,
  (SELECT r.value FROM reactions r WHERE r.meme_id = m.id AND r.username = ?) AS my_reaction
  FROM memes m";
if ($cat === 'All') {
    $stmt = $pdo->prepare("$select ORDER BY m.ts DESC");
    $stmt->execute([$me]);
} else {
    $stmt = $pdo->prepare("$select WHERE m.category = ? ORDER BY m.ts DESC");
    $stmt->execute([$me, $cat]);
}
$memes = $stmt->fetchAll();

$pageTitle = 'Home';
require 'header.php';
?>
<nav class="filters" aria-label="Meme categories">
  <?php foreach (array_merge(['All'], CATEGORIES) as $c): ?>
    <a class="chip <?= $c === $cat ? 'active' : '' ?>" href="index.php?category=<?= urlencode($c) ?>"><?= e($c) ?></a>
  <?php endforeach; ?>
</nav>

<?php if (!$memes): ?>
  <p class="empty">No memes here yet. <?= $cat === 'All' ? 'Log in and upload the first one!' : 'Try another category or upload one yourself.' ?></p>
<?php else: ?>
  <section class="grid">
    <?php foreach ($memes as $m): ?>
      <button type="button" class="tile" data-lightbox="meme-<?= e($m['id']) ?>">
        <span class="badge"><?= e($m['category']) ?></span>
        <img src="uploads/<?= e($m['image_path']) ?>" alt="<?= e($m['caption'] ?: 'Meme by ' . $m['uploader_name']) ?>" loading="lazy">
        <span class="tile-by"><?= e($m['uploader_name']) ?></span>
        <span class="stats">👍 <?= (int)$m['likes'] ?> &nbsp; 👎 <?= (int)$m['dislikes'] ?></span>
      </button>
    <?php endforeach; ?>
  </section>

  <?php foreach ($memes as $m): ?>
    <dialog class="modal lightbox" id="meme-<?= e($m['id']) ?>">
      <button type="button" class="modal-close" data-close aria-label="Close">✕</button>
      <img src="uploads/<?= e($m['image_path']) ?>" alt="<?= e($m['caption'] ?: 'Meme') ?>">
      <div class="lb-info">
        <p class="lb-user"><strong><?= e($m['uploader_name']) ?></strong> <span class="muted">@<?= e($m['uploader']) ?></span></p>
        <?php if ($m['caption'] !== null && $m['caption'] !== ''): ?><p><?= nl2br(e($m['caption'])) ?></p><?php endif; ?>
        <span class="badge"><?= e($m['category']) ?></span>
        <div class="reactions">
          <?php foreach (['like' => ['👍', 1, 'likes'], 'dislike' => ['👎', -1, 'dislikes']] as $type => [$icon, $val, $col]): ?>
            <?php if ($me === ''): ?>
              <button type="button" class="react" data-login-required title="Log in to react"><?= $icon ?> <?= (int)$m[$col] ?></button>
            <?php else: ?>
              <form method="POST" action="react.php">
                <input type="hidden" name="meme_id" value="<?= e($m['id']) ?>">
                <input type="hidden" name="category" value="<?= e($cat) ?>">
                <input type="hidden" name="value" value="<?= $type ?>">
                <button type="submit" class="react <?= (int)$m['my_reaction'] === $val ? 'on' : '' ?>" aria-pressed="<?= (int)$m['my_reaction'] === $val ? 'true' : 'false' ?>"><?= $icon ?> <?= (int)$m[$col] ?></button>
              </form>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <?php if ($me === ''): ?><p class="muted hint-login">Log in to like or dislike this meme.</p><?php endif; ?>
      </div>
    </dialog>
  <?php endforeach; ?>
<?php endif; ?>
<?php require 'footer.php'; ?>
