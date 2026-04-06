<?php
require_once 'config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) redirect('index.php');

// Ambil artikel
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug,
           u.full_name as author_name, u.username as author_username, u.bio as author_bio
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    JOIN users u ON a.author_id = u.id
    WHERE a.slug = ? AND a.status = 'published'
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    header("HTTP/1.0 404 Not Found");
    die("Artikel tidak ditemukan.");
}

// Update view count
$pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article['id']]);

// Ambil tags artikel
$tags = $pdo->prepare("SELECT t.* FROM tags t JOIN article_tags at ON t.id = at.tag_id WHERE at.article_id = ?");
$tags->execute([$article['id']]);
$articleTags = $tags->fetchAll();

// Proses komentar baru
$commentError = '';
$commentSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    if (!$name || !$email || !$content) {
        $commentError = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $commentError = 'Format email tidak valid.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO comments (article_id, parent_id, name, email, content, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$article['id'], $parent_id, $name, $email, $content]);
        $commentSuccess = 'Komentar Anda telah dikirim dan menunggu moderasi.';
    }
}

// Ambil komentar approved
$commentStmt = $pdo->prepare("
    SELECT * FROM comments 
    WHERE article_id = ? AND status = 'approved' AND parent_id IS NULL
    ORDER BY created_at ASC
");
$commentStmt->execute([$article['id']]);
$comments = $commentStmt->fetchAll();

// Ambil balasan untuk setiap komentar
foreach ($comments as &$comment) {
    $replyStmt = $pdo->prepare("SELECT * FROM comments WHERE parent_id = ? AND status = 'approved' ORDER BY created_at ASC");
    $replyStmt->execute([$comment['id']]);
    $comment['replies'] = $replyStmt->fetchAll();
}

// Artikel terkait
$related = $pdo->prepare("
    SELECT a.*, u.full_name as author_name FROM articles a 
    JOIN users u ON a.author_id = u.id
    WHERE a.category_id = ? AND a.id != ? AND a.status = 'published' 
    ORDER BY a.created_at DESC LIMIT 3
");
$related->execute([$article['category_id'], $article['id']]);
$relatedArticles = $related->fetchAll();

// Sidebar data
$categories = $pdo->query("SELECT c.*, COUNT(a.id) as count FROM categories c LEFT JOIN articles a ON c.id = a.category_id AND a.status='published' GROUP BY c.id ORDER BY count DESC")->fetchAll();
$popular = $pdo->query("SELECT a.*, u.full_name as author_name FROM articles a JOIN users u ON a.author_id = u.id WHERE a.status='published' ORDER BY a.views DESC LIMIT 5")->fetchAll();
$allTags = $pdo->query("SELECT t.*, COUNT(at.article_id) as count FROM tags t LEFT JOIN article_tags at ON t.id = at.tag_id GROUP BY t.id ORDER BY count DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= escape($article['title']) ?> — <?= APP_NAME ?></title>
<meta name="description" content="<?= escape($article['excerpt']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'partials/header.php'; ?>

<main class="container">
  <div class="layout-grid">
    <article class="main-content article-single">

      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <a href="index.php">Beranda</a> <span>›</span>
        <a href="index.php?category=<?= $article['category_slug'] ?>"><?= escape($article['category_name']) ?></a> <span>›</span>
        <span><?= escape(substr($article['title'], 0, 50)) ?>...</span>
      </nav>

      <!-- Header Artikel -->
      <header class="article-header">
        <div class="meta">
          <a href="index.php?category=<?= $article['category_slug'] ?>" class="category-badge"><?= escape($article['category_name']) ?></a>
          <span class="dot">·</span>
          <time><?= date('d F Y', strtotime($article['created_at'])) ?></time>
          <span class="dot">·</span>
          <span>👁 <?= number_format($article['views']) ?> tampilan</span>
        </div>
        <h1 class="article-title"><?= escape($article['title']) ?></h1>
        <?php if ($article['excerpt']): ?>
          <p class="article-excerpt"><?= escape($article['excerpt']) ?></p>
        <?php endif; ?>
        <div class="author-info">
          <div class="author-avatar"><?= strtoupper(substr($article['author_name'], 0, 1)) ?></div>
          <div>
            <strong><?= escape($article['author_name']) ?></strong>
            <span>@<?= escape($article['author_username']) ?></span>
          </div>
        </div>
      </header>

      <!-- Thumbnail -->
      <?php if ($article['thumbnail']): ?>
        <div class="article-thumbnail">
          <img src="<?= escape(UPLOAD_URL . $article['thumbnail']) ?>" alt="<?= escape($article['title']) ?>">
        </div>
      <?php endif; ?>

      <!-- Konten Artikel -->
      <div class="article-content">
        <?= $article['content'] ?>
      </div>

      <!-- Tags -->
      <?php if (!empty($articleTags)): ?>
        <div class="article-tags">
          <strong>Tag:</strong>
          <?php foreach ($articleTags as $tag): ?>
            <a href="index.php?tag=<?= $tag['slug'] ?>" class="tag-pill">#<?= escape($tag['name']) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Biodata Penulis -->
      <div class="author-box">
        <div class="author-avatar lg"><?= strtoupper(substr($article['author_name'], 0, 1)) ?></div>
        <div>
          <h4>Tentang Penulis</h4>
          <strong><?= escape($article['author_name']) ?></strong>
          <p><?= escape($article['author_bio'] ?? 'Penulis di ' . APP_NAME) ?></p>
        </div>
      </div>

      <!-- Artikel Terkait -->
      <?php if (!empty($relatedArticles)): ?>
        <div class="related-articles">
          <h3>Artikel Terkait</h3>
          <div class="related-grid">
            <?php foreach ($relatedArticles as $r): ?>
              <a href="artikel.php?slug=<?= $r['slug'] ?>" class="related-card">
                <div class="related-img"><?= empty($r['thumbnail']) ? '📝' : '<img src="'.escape(UPLOAD_URL.$r['thumbnail']).'" alt="">' ?></div>
                <span><?= escape($r['title']) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Komentar -->
      <section class="comments-section">
        <h3>Komentar (<?= count($comments) ?>)</h3>

        <?php if ($commentSuccess): ?>
          <div class="alert success"><?= $commentSuccess ?></div>
        <?php endif; ?>
        <?php if ($commentError): ?>
          <div class="alert error"><?= $commentError ?></div>
        <?php endif; ?>

        <!-- Form Komentar -->
        <div class="comment-form-wrap">
          <h4>Tinggalkan Komentar</h4>
          <form method="POST" class="comment-form">
            <input type="hidden" name="parent_id" id="parent_id" value="">
            <div class="form-row">
              <div class="form-group">
                <label>Nama *</label>
                <input type="text" name="name" required placeholder="Nama Anda">
              </div>
              <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required placeholder="email@contoh.com">
              </div>
            </div>
            <div class="form-group">
              <label>Komentar *</label>
              <textarea name="content" rows="4" required placeholder="Tulis komentar Anda..."></textarea>
            </div>
            <div id="reply-info" class="reply-info" style="display:none">
              Membalas: <span id="reply-name"></span>
              <button type="button" onclick="cancelReply()">✕ Batal</button>
            </div>
            <button type="submit" name="submit_comment" class="btn-primary">Kirim Komentar</button>
          </form>
        </div>

        <!-- Daftar Komentar -->
        <div class="comments-list">
          <?php foreach ($comments as $comment): ?>
            <div class="comment" id="comment-<?= $comment['id'] ?>">
              <div class="comment-avatar"><?= strtoupper(substr($comment['name'], 0, 1)) ?></div>
              <div class="comment-body">
                <div class="comment-meta">
                  <strong><?= escape($comment['name']) ?></strong>
                  <time><?= timeAgo($comment['created_at']) ?></time>
                </div>
                <p><?= escape($comment['content']) ?></p>
                <button class="reply-btn" onclick="replyTo(<?= $comment['id'] ?>, '<?= escape($comment['name']) ?>')">Balas</button>

                <!-- Balasan -->
                <?php foreach ($comment['replies'] as $reply): ?>
                  <div class="comment reply">
                    <div class="comment-avatar sm"><?= strtoupper(substr($reply['name'], 0, 1)) ?></div>
                    <div class="comment-body">
                      <div class="comment-meta">
                        <strong><?= escape($reply['name']) ?></strong>
                        <time><?= timeAgo($reply['created_at']) ?></time>
                      </div>
                      <p><?= escape($reply['content']) ?></p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if (empty($comments)): ?>
            <div class="empty-state sm">
              <span>💬</span>
              <p>Belum ada komentar. Jadilah yang pertama!</p>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </article>

    <aside class="sidebar">
      <?php include 'partials/sidebar.php'; ?>
    </aside>
  </div>
</main>

<?php include 'partials/footer.php'; ?>
<script>
function replyTo(id, name) {
  document.getElementById('parent_id').value = id;
  document.getElementById('reply-name').textContent = name;
  document.getElementById('reply-info').style.display = 'flex';
  document.querySelector('.comment-form-wrap').scrollIntoView({behavior:'smooth'});
}
function cancelReply() {
  document.getElementById('parent_id').value = '';
  document.getElementById('reply-info').style.display = 'none';
}
</script>
</body>
</html>
