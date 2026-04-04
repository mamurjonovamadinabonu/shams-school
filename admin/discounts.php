<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
$db = getDB();
$pageTitle = 'Chegirmalar';

$action = $_GET['action'] ?? '';

if ($action === 'delete' && isset($_GET['id'])) {
    $db->prepare("DELETE FROM discounts WHERE id=?")->execute([$_GET['id']]);
    setFlash('success', 'Chegirma o\'chirildi');
    header('Location: discounts.php'); exit();
}

if ($action === 'toggle' && isset($_GET['id'])) {
    $db->prepare("UPDATE discounts SET active = CASE WHEN active=1 THEN 0 ELSE 1 END WHERE id=?")
       ->execute([$_GET['id']]);
    header('Location: discounts.php'); exit();
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title']??'');
    $desc  = trim($_POST['description']??'');
    $pct   = (int)($_POST['percent']??0);
    $exp   = trim($_POST['expires_at']??'') ?: null;
    $active = isset($_POST['active'])?1:0;

    if ($id > 0) {
        $db->prepare("UPDATE discounts SET title=?,description=?,percent=?,expires_at=?,active=? WHERE id=?")
           ->execute([$title,$desc,$pct,$exp,$active,$id]);
        setFlash('success','Chegirma yangilandi');
    } else {
        $db->prepare("INSERT INTO discounts (title,description,percent,expires_at,active) VALUES(?,?,?,?,?)")
           ->execute([$title,$desc,$pct,$exp,$active]);
        setFlash('success',"Chegirma qo'shildi");
    }
    header('Location: discounts.php'); exit();
}

$discounts = $db->query("SELECT * FROM discounts ORDER BY active DESC, id DESC")->fetchAll();
$flash = getFlash();
include __DIR__ . '/includes/layout.php';
?>

<?php if($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>"><?= $flash['type']==='success'?'✅':'❌' ?> <?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h2>🎁 Chegirmalar</h2>
    <p>Maktab chegirmalari va imtiyozlarini boshqarish</p>
  </div>
  <button class="btn btn-primary" onclick="openAddModal()">+ Chegirma qo'shish</button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px">
  <?php foreach($discounts as $d): ?>
  <div class="panel" style="padding:0;border-color:<?= $d['active']?'rgba(245,166,35,0.25)':'var(--border)' ?>">
    <div style="padding:20px 22px;position:relative">
      <!-- Percent badge -->
      <div style="position:absolute;top:16px;right:16px;width:56px;height:56px;border-radius:50%;
                  background:<?= $d['active']?'linear-gradient(135deg,var(--accent),#e09520)':'rgba(100,116,139,0.2)' ?>;
                  display:flex;align-items:center;justify-content:center;flex-direction:column;text-align:center">
        <span style="font-size:1rem;font-weight:900;color:var(--primary-dark)">-<?= $d['percent'] ?>%</span>
      </div>

      <span class="badge badge-<?= $d['active'] ?>" style="margin-bottom:12px;display:inline-block"><?= $d['active']?'Faol':'Nofaol' ?></span>
      <h3 style="font-size:1rem;font-weight:700;color:var(--white);margin-bottom:8px;padding-right:68px">
        <?= htmlspecialchars($d['title']) ?>
      </h3>
      <p style="font-size:0.82rem;color:var(--muted);line-height:1.5;margin-bottom:12px">
        <?= htmlspecialchars($d['description']) ?>
      </p>
      <?php if($d['expires_at']): ?>
      <div style="font-size:0.75rem;color:var(--muted)">
        ⏰ Muddati: <strong style="color:var(--text)"><?= date('d.m.Y', strtotime($d['expires_at'])) ?></strong>
      </div>
      <?php else: ?>
      <div style="font-size:0.75rem;color:var(--muted)">⏰ Muddatsiz</div>
      <?php endif; ?>
    </div>
    <div style="padding:12px 22px;border-top:1px solid var(--border);display:flex;gap:8px">
      <a href="?action=toggle&id=<?= $d['id'] ?>" class="btn-sm">
        <?= $d['active']?'⏸ O\'chirish':'▶ Yoqish' ?>
      </a>
      <button class="btn-sm" onclick='openEditModal(<?= htmlspecialchars(json_encode($d)) ?>)'>✏️ Tahrirlash</button>
      <a href="?action=delete&id=<?= $d['id'] ?>" class="btn-sm" style="color:#fca5a5"
         data-confirm="Chegirmani o'chirmoqchimisiz?">🗑</a>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if(empty($discounts)): ?>
  <div class="panel text-center muted" style="padding:40px;grid-column:1/-1">
    Chegirmalar mavjud emas
  </div>
  <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal-overlay" id="discountModal">
  <div class="modal" style="max-width:560px">
    <div class="modal-head">
      <h3 id="dModalTitle">🎁 Chegirma qo'shish</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <form method="POST">
      <input type="hidden" name="id" id="dId" value="0">
      <div class="form-group">
        <label class="form-label">Sarlavha *</label>
        <input type="text" name="title" id="dTitle" class="form-control" required placeholder="Chegirma nomi">
      </div>
      <div class="form-group">
        <label class="form-label">Tavsif *</label>
        <textarea name="description" id="dDesc" class="form-control" rows="3" required placeholder="Chegirma haqida..."></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Chegirma foizi (%)</label>
          <input type="number" name="percent" id="dPct" class="form-control" min="1" max="100" value="10">
        </div>
        <div class="form-group">
          <label class="form-label">Tugash sanasi</label>
          <input type="date" name="expires_at" id="dExp" class="form-control">
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px">
        <input type="checkbox" name="active" id="dActive" value="1" checked style="width:auto">
        <label for="dActive" style="font-size:0.875rem">Faol holat</label>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="btn btn-primary">💾 Saqlash</button>
        <button type="button" class="btn btn-info" onclick="closeModal()">Bekor</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddModal(){
  document.getElementById('dModalTitle').textContent="🎁 Chegirma qo'shish";
  ['dTitle','dDesc','dExp'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('dId').value=0;
  document.getElementById('dPct').value=10;
  document.getElementById('dActive').checked=true;
  document.getElementById('discountModal').classList.add('open');
}
function openEditModal(d){
  document.getElementById('dModalTitle').textContent="✏️ Chegirmani tahrirlash";
  document.getElementById('dId').value=d.id;
  document.getElementById('dTitle').value=d.title||'';
  document.getElementById('dDesc').value=d.description||'';
  document.getElementById('dPct').value=d.percent||10;
  document.getElementById('dExp').value=d.expires_at||'';
  document.getElementById('dActive').checked=d.active==1;
  document.getElementById('discountModal').classList.add('open');
}
function closeModal(){document.querySelectorAll('.modal-overlay').forEach(m=>m.classList.remove('open'))}
document.querySelectorAll('.modal-overlay').forEach(m=>{m.addEventListener('click',e=>{if(e.target===m)closeModal()})});
</script>

<?php include __DIR__ . '/includes/layout_end.php'; ?>
