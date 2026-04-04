<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();

$db = getDB();

// Statistikalar
$stats = [
    'applications' => $db->query("SELECT COUNT(*) FROM applications")->fetchColumn(),
    'new_apps'     => $db->query("SELECT COUNT(*) FROM applications WHERE status='new'")->fetchColumn(),
    'teachers'     => $db->query("SELECT COUNT(*) FROM teachers WHERE active=1")->fetchColumn(),
    'news'         => $db->query("SELECT COUNT(*) FROM news WHERE published=1")->fetchColumn(),
    'discounts'    => $db->query("SELECT COUNT(*) FROM discounts WHERE active=1")->fetchColumn(),
];

$recent_apps = $db->query("SELECT * FROM applications ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_news = $db->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 3")->fetchAll();

$flash = getFlash();
$pageTitle = 'Dashboard';
include __DIR__ . '/includes/layout.php';
?>

<?php if($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>">
  <?= $flash['type']==='success' ? '✅' : '❌' ?> <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="stats-grid">
  <div class="stat-card accent">
    <div class="stat-icon">📋</div>
    <div class="stat-info">
      <span class="stat-num"><?= $stats['applications'] ?></span>
      <span class="stat-lbl">Jami arizalar</span>
    </div>
    <div class="stat-badge new"><?= $stats['new_apps'] ?> yangi</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon">👨‍🏫</div>
    <div class="stat-info">
      <span class="stat-num"><?= $stats['teachers'] ?></span>
      <span class="stat-lbl">O'qituvchilar</span>
    </div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon">📰</div>
    <div class="stat-info">
      <span class="stat-num"><?= $stats['news'] ?></span>
      <span class="stat-lbl">Yangiliklar</span>
    </div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon">🎁</div>
    <div class="stat-info">
      <span class="stat-num"><?= $stats['discounts'] ?></span>
      <span class="stat-lbl">Faol chegirmalar</span>
    </div>
  </div>
</div>

<div class="dashboard-grid">
  <!-- So'nggi arizalar -->
  <div class="panel">
    <div class="panel-head">
      <h3>📋 So'nggi Arizalar</h3>
      <a href="applications.php" class="btn-sm">Barchasi →</a>
    </div>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr><th>Ism Familya</th><th>Sinf</th><th>Yo'nalish</th><th>Holat</th><th>Sana</th></tr>
        </thead>
        <tbody>
          <?php if(empty($recent_apps)): ?>
          <tr><td colspan="5" class="text-center muted">Arizalar mavjud emas</td></tr>
          <?php else: foreach($recent_apps as $a): ?>
          <tr>
            <td><strong><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></strong></td>
            <td><?= htmlspecialchars($a['grade']) ?></td>
            <td><?= htmlspecialchars($a['direction']) ?></td>
            <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            <td class="muted"><?= date('d.m.Y', strtotime($a['created_at'])) ?></td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- So'nggi yangiliklar -->
  <div class="panel">
    <div class="panel-head">
      <h3>📰 So'nggi Yangiliklar</h3>
      <a href="news.php" class="btn-sm">Barchasi →</a>
    </div>
    <div class="news-list">
      <?php if(empty($recent_news)): ?>
      <p class="muted">Yangiliklar mavjud emas</p>
      <?php else: foreach($recent_news as $n): ?>
      <div class="news-item">
        <div class="news-cat"><?= htmlspecialchars($n['category']) ?></div>
        <p class="news-title"><?= htmlspecialchars($n['title']) ?></p>
        <span class="muted small"><?= date('d.m.Y', strtotime($n['created_at'])) ?></span>
      </div>
      <?php endforeach; endif; ?>
    </div>
    <a href="news.php?action=add" class="btn-add-news">+ Yangilik qo'shish</a>
  </div>
</div>

<?php include __DIR__ . '/includes/layout_end.php'; ?>
