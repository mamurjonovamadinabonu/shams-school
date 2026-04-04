<?php
// Layout header — layout.php ichida chaqiriladi
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — SHAMS Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ===================== RESET & ROOT ===================== */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0C2B4E;--primary-light:#1a4a7e;--primary-dark:#071c33;
  --accent:#F5A623;--accent-dark:#d4881a;
  --white:#ffffff;--bg:#0f1923;--sidebar:#0a1628;
  --panel:rgba(255,255,255,0.04);--border:rgba(255,255,255,0.07);
  --text:#e2e8f0;--muted:#64748b;--muted2:rgba(255,255,255,0.4);
  --success:#22c55e;--danger:#ef4444;--warning:#f59e0b;--info:#3b82f6;
  --radius:10px;--radius-lg:16px;
  --shadow:0 4px 20px rgba(0,0,0,0.3);
  --transition:all .25s ease;
  --sidebar-w:260px;
}
html,body{height:100%;font-family:'Inter',sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}
ul{list-style:none}
img{max-width:100%}
button,input,select,textarea{font-family:inherit}

/* ===================== SCROLLBAR ===================== */
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:var(--sidebar)}
::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.12);border-radius:3px}

/* ===================== LAYOUT ===================== */
.admin-wrap{display:flex;min-height:100vh}

/* ——— SIDEBAR ——— */
.sidebar{
  width:var(--sidebar-w);min-width:var(--sidebar-w);
  background:var(--sidebar);
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;
  position:sticky;top:0;height:100vh;overflow-y:auto;
  z-index:100;
}
.sidebar-brand{
  padding:24px 20px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:12px;
}
.brand-icon{
  width:42px;height:42px;border-radius:10px;
  background:linear-gradient(135deg,var(--primary),var(--primary-light));
  display:flex;align-items:center;justify-content:center;
  font-size:1.1rem;font-weight:900;color:var(--accent);
  flex-shrink:0;
  box-shadow:0 4px 12px rgba(12,43,78,0.5);
}
.brand-text span:first-child{
  display:block;font-size:0.95rem;font-weight:700;color:var(--white);
}
.brand-text span:last-child{
  font-size:0.72rem;color:var(--muted);
}

.sidebar-nav{flex:1;padding:16px 12px}
.nav-section{margin-bottom:8px}
.nav-section-title{
  font-size:0.65rem;font-weight:700;color:var(--muted);
  text-transform:uppercase;letter-spacing:1.5px;
  padding:0 8px;margin-bottom:6px;margin-top:16px;
}
.nav-link{
  display:flex;align-items:center;gap:10px;
  padding:10px 12px;border-radius:var(--radius);
  font-size:0.875rem;font-weight:500;color:var(--muted2);
  transition:var(--transition);cursor:pointer;
  margin-bottom:2px;
}
.nav-link:hover{background:rgba(255,255,255,0.06);color:var(--white)}
.nav-link.active{background:rgba(245,166,35,0.12);color:var(--accent);font-weight:600}
.nav-link .ico{width:20px;text-align:center;font-size:1rem}
.nav-link .badge-count{
  margin-left:auto;background:var(--danger);
  color:#fff;font-size:0.65rem;font-weight:700;
  padding:2px 7px;border-radius:20px;
}

.sidebar-footer{
  padding:16px;border-top:1px solid var(--border);
}
.admin-user{
  display:flex;align-items:center;gap:10px;margin-bottom:12px;
}
.user-avatar{
  width:36px;height:36px;border-radius:50%;
  background:linear-gradient(135deg,var(--accent),var(--accent-dark));
  display:flex;align-items:center;justify-content:center;
  font-size:0.9rem;font-weight:700;color:var(--primary-dark);
}
.user-info span:first-child{display:block;font-size:0.85rem;font-weight:600;color:var(--white)}
.user-info span:last-child{font-size:0.72rem;color:var(--muted)}
.btn-logout{
  display:flex;align-items:center;gap:8px;
  padding:9px 14px;border-radius:var(--radius);
  background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);
  color:#fca5a5;font-size:0.82rem;font-weight:600;
  transition:var(--transition);width:100%;cursor:pointer;
}
.btn-logout:hover{background:rgba(239,68,68,0.2);border-color:rgba(239,68,68,0.4)}

/* ——— MAIN ——— */
.main{flex:1;display:flex;flex-direction:column;min-width:0}

.topbar{
  background:var(--sidebar);border-bottom:1px solid var(--border);
  padding:0 28px;height:64px;
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:50;
}
.topbar-left{display:flex;align-items:center;gap:16px}
.hamburger-btn{
  display:none;background:none;border:none;color:var(--text);
  font-size:1.3rem;cursor:pointer;
}
.page-title-h{font-size:1.1rem;font-weight:700;color:var(--white)}
.page-breadcrumb{font-size:0.78rem;color:var(--muted)}

.topbar-right{display:flex;align-items:center;gap:12px}
.topbar-time{font-size:0.78rem;color:var(--muted)}
.btn-site{
  display:flex;align-items:center;gap:6px;
  padding:8px 14px;border-radius:var(--radius);
  background:rgba(245,166,35,0.12);border:1px solid rgba(245,166,35,0.2);
  color:var(--accent);font-size:0.8rem;font-weight:600;
  transition:var(--transition);
}
.btn-site:hover{background:rgba(245,166,35,0.2)}

.content{flex:1;padding:28px;max-width:1400px;width:100%}

/* ===================== COMPONENTS ===================== */

/* Flash */
.flash{
  padding:14px 18px;border-radius:var(--radius);
  font-size:0.875rem;font-weight:500;margin-bottom:24px;
  display:flex;align-items:center;gap:8px;
  animation:slideDown .3s ease;
}
@keyframes slideDown{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}
.flash-success{background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.25);color:#86efac}
.flash-error{background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);color:#fca5a5}
.flash-info{background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.25);color:#93c5fd}

/* Stats grid */
.stats-grid{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
  gap:18px;margin-bottom:28px;
}
.stat-card{
  border-radius:var(--radius-lg);padding:22px;
  border:1px solid var(--border);
  display:flex;align-items:center;gap:16px;
  position:relative;overflow:hidden;
  transition:var(--transition);
}
.stat-card::before{
  content:'';position:absolute;inset:0;opacity:0.04;
  background:radial-gradient(circle at top right,currentColor,transparent);
}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow)}
.stat-card.accent{background:rgba(245,166,35,0.08);border-color:rgba(245,166,35,0.2)}
.stat-card.blue{background:rgba(59,130,246,0.08);border-color:rgba(59,130,246,0.2)}
.stat-card.green{background:rgba(34,197,94,0.08);border-color:rgba(34,197,94,0.2)}
.stat-card.purple{background:rgba(168,85,247,0.08);border-color:rgba(168,85,247,0.2)}
.stat-icon{font-size:1.8rem}
.stat-info{flex:1}
.stat-num{display:block;font-size:1.8rem;font-weight:800;color:var(--white);line-height:1}
.stat-lbl{font-size:0.78rem;color:var(--muted);margin-top:4px}
.stat-badge{
  position:absolute;top:12px;right:12px;
  font-size:0.65rem;font-weight:700;padding:3px 8px;
  border-radius:20px;
}
.stat-badge.new{background:rgba(239,68,68,0.2);color:#fca5a5}

/* Dashboard grid */
.dashboard-grid{display:grid;grid-template-columns:1.6fr 1fr;gap:20px}
@media(max-width:900px){.dashboard-grid{grid-template-columns:1fr}}

/* Panel */
.panel{
  background:var(--panel);border:1px solid var(--border);
  border-radius:var(--radius-lg);padding:0;overflow:hidden;
}
.panel-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:18px 24px;border-bottom:1px solid var(--border);
}
.panel-head h3{font-size:0.95rem;font-weight:700;color:var(--white)}

/* Table */
.table-wrap{overflow-x:auto}
.table{width:100%;border-collapse:collapse}
.table thead tr{border-bottom:1px solid var(--border)}
.table th{
  padding:12px 16px;font-size:0.72rem;font-weight:700;
  color:var(--muted);text-transform:uppercase;letter-spacing:1px;
  text-align:left;white-space:nowrap;
}
.table td{
  padding:12px 16px;font-size:0.875rem;
  border-bottom:1px solid rgba(255,255,255,0.04);
}
.table tbody tr:last-child td{border-bottom:none}
.table tbody tr:hover td{background:rgba(255,255,255,0.02)}

/* Badge */
.badge{padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;white-space:nowrap}
.badge-new{background:rgba(59,130,246,0.2);color:#93c5fd}
.badge-contacted{background:rgba(245,166,35,0.2);color:#fcd34d}
.badge-enrolled{background:rgba(34,197,94,0.2);color:#86efac}
.badge-cancelled{background:rgba(239,68,68,0.2);color:#fca5a5}
.badge-1{background:rgba(34,197,94,0.2);color:#86efac}
.badge-0{background:rgba(239,68,68,0.2);color:#fca5a5}

/* News list */
.news-list{padding:8px 0}
.news-item{padding:14px 24px;border-bottom:1px solid rgba(255,255,255,0.04)}
.news-item:last-child{border-bottom:none}
.news-cat{
  display:inline-block;font-size:0.65rem;font-weight:700;
  background:rgba(245,166,35,0.15);color:var(--accent);
  padding:2px 8px;border-radius:20px;margin-bottom:6px;
  text-transform:uppercase;letter-spacing:1px;
}
.news-title{font-size:0.875rem;font-weight:600;color:var(--white);margin-bottom:4px}
.btn-add-news{
  display:block;text-align:center;
  padding:12px;border-top:1px solid var(--border);
  font-size:0.8rem;font-weight:600;color:var(--accent);
  transition:var(--transition);
}
.btn-add-news:hover{background:rgba(245,166,35,0.06)}

/* Buttons */
.btn-sm{
  display:inline-flex;align-items:center;gap:6px;
  padding:6px 14px;border-radius:8px;
  font-size:0.78rem;font-weight:600;
  background:rgba(255,255,255,0.06);border:1px solid var(--border);
  color:var(--muted2);transition:var(--transition);cursor:pointer;
}
.btn-sm:hover{background:rgba(255,255,255,0.1);color:var(--white)}
.btn{
  display:inline-flex;align-items:center;gap:8px;
  padding:10px 20px;border-radius:var(--radius);
  font-size:0.875rem;font-weight:600;
  border:none;cursor:pointer;transition:var(--transition);
}
.btn-primary{background:var(--accent);color:var(--primary-dark)}
.btn-primary:hover{background:var(--accent-dark)}
.btn-success{background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);color:#86efac}
.btn-success:hover{background:rgba(34,197,94,0.25)}
.btn-danger{background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);color:#fca5a5}
.btn-danger:hover{background:rgba(239,68,68,0.22)}
.btn-info{background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.25);color:#93c5fd}
.btn-info:hover{background:rgba(59,130,246,0.22)}

/* Form */
.form-panel{
  background:var(--panel);border:1px solid var(--border);
  border-radius:var(--radius-lg);padding:28px;
}
.form-group{margin-bottom:20px}
.form-label{
  display:block;font-size:0.78rem;font-weight:600;
  color:rgba(255,255,255,0.5);margin-bottom:8px;
  text-transform:uppercase;letter-spacing:1px;
}
.form-control{
  width:100%;padding:11px 16px;
  background:rgba(255,255,255,0.05);
  border:1px solid var(--border);border-radius:var(--radius);
  color:var(--text);font-size:0.9rem;
  transition:var(--transition);outline:none;
}
.form-control:focus{
  border-color:var(--accent);
  background:rgba(255,255,255,0.08);
  box-shadow:0 0 0 3px rgba(245,166,35,0.1);
}
textarea.form-control{resize:vertical;min-height:120px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}

/* Page header */
.page-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:24px;
}
.page-header h2{font-size:1.3rem;font-weight:800;color:var(--white)}
.page-header p{font-size:0.85rem;color:var(--muted);margin-top:2px}

/* Actions col */
.actions{display:flex;gap:6px;align-items:center}

/* Utility */
.text-center{text-align:center}.muted{color:var(--muted)}.small{font-size:0.78rem}
.mt-4{margin-top:16px}.mb-4{margin-bottom:16px}
.flex{display:flex}.gap-2{gap:8px}.gap-3{gap:12px}.items-center{align-items:center}

/* Pagination */
.pagination{display:flex;gap:6px;margin-top:20px;justify-content:center}
.page-btn{
  width:36px;height:36px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:0.85rem;font-weight:600;
  background:var(--panel);border:1px solid var(--border);
  color:var(--muted2);transition:var(--transition);cursor:pointer;
}
.page-btn.active,.page-btn:hover{background:rgba(245,166,35,0.15);border-color:rgba(245,166,35,0.3);color:var(--accent)}

/* Modal */
.modal-overlay{
  position:fixed;inset:0;background:rgba(0,0,0,0.7);
  display:flex;align-items:center;justify-content:center;z-index:1000;
  opacity:0;pointer-events:none;transition:opacity .3s;
}
.modal-overlay.open{opacity:1;pointer-events:auto}
.modal{
  background:#182030;border:1px solid var(--border);
  border-radius:var(--radius-lg);padding:28px;
  width:90%;max-width:540px;
  transform:scale(0.95);transition:transform .3s;
}
.modal-overlay.open .modal{transform:scale(1)}
.modal-head{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:20px;
}
.modal-head h3{font-size:1rem;font-weight:700;color:var(--white)}
.modal-close{
  background:none;border:none;color:var(--muted);
  font-size:1.3rem;cursor:pointer;
}

/* Mobile */
@media(max-width:768px){
  .sidebar{position:fixed;left:-260px;transition:left .3s;height:100vh}
  .sidebar.open{left:0}
  .hamburger-btn{display:flex}
  .form-row{grid-template-columns:1fr}
  .stats-grid{grid-template-columns:1fr 1fr}
  .content{padding:16px}
  .topbar{padding:0 16px}
}
</style>
</head>
<body>
<div class="admin-wrap">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">S</div>
      <div class="brand-text">
        <span>SHAMS Admin</span>
        <span>Boshqaruv paneli</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">Asosiy</div>
      <a href="index.php" class="nav-link <?= $currentPage==='index'?'active':'' ?>">
        <span class="ico">🏠</span> Dashboard
      </a>

      <div class="nav-section-title">Boshqaruv</div>
      <a href="applications.php" class="nav-link <?= $currentPage==='applications'?'active':'' ?>">
        <span class="ico">📋</span> Arizalar
        <?php
        $newCount = getDB()->query("SELECT COUNT(*) FROM applications WHERE status='new'")->fetchColumn();
        if($newCount > 0): ?>
        <span class="badge-count"><?= $newCount ?></span>
        <?php endif; ?>
      </a>
      <a href="teachers.php" class="nav-link <?= $currentPage==='teachers'?'active':'' ?>">
        <span class="ico">👨‍🏫</span> O'qituvchilar
      </a>
      <a href="news.php" class="nav-link <?= $currentPage==='news'?'active':'' ?>">
        <span class="ico">📰</span> Yangiliklar
      </a>
      <a href="discounts.php" class="nav-link <?= $currentPage==='discounts'?'active':'' ?>">
        <span class="ico">🎁</span> Chegirmalar
      </a>

      <div class="nav-section-title">Tizim</div>
      <a href="settings.php" class="nav-link <?= $currentPage==='settings'?'active':'' ?>">
        <span class="ico">⚙️</span> Sozlamalar
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="admin-user">
        <div class="user-avatar">A</div>
        <div class="user-info">
          <span><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></span>
          <span>Superadmin</span>
        </div>
      </div>
      <a href="logout.php" class="btn-logout">
        🚪 Chiqish
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
        <div>
          <div class="page-title-h"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></div>
          <div class="page-breadcrumb">SHAMS School → <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></div>
        </div>
      </div>
      <div class="topbar-right">
        <span class="topbar-time" id="clock"></span>
        <a href="../index.html" target="_blank" class="btn-site">🌐 Sayt</a>
      </div>
    </header>

    <div class="content">
