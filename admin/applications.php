<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
$db = getDB();
$pageTitle = 'Arizalar';

// Actions
$action = $_GET['action'] ?? '';
if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM applications WHERE id=?");
    $stmt->execute([$_GET['id']]);
    setFlash('success', 'Ariza o\'chirildi');
    header('Location: applications.php'); exit();
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])) {
    $stmt = $db->prepare("UPDATE applications SET status=?, note=? WHERE id=?");
    $stmt->execute([$_POST['status'], $_POST['note'], $_POST['id']]);
    setFlash('success', 'Ariza yangilandi');
    header('Location: applications.php'); exit();
}

// Filters
$search    = trim($_GET['q'] ?? '');
$statusF   = trim($_GET['status'] ?? '');
$page      = max(1, (int)($_GET['p'] ?? 1));
$perPage   = 15;
$offset    = ($page-1)*$perPage;

$where = '1=1';
$params = [];
if ($search) {
    $where .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?)";
    $params = array_merge($params, ["%$search%","%$search%","%$search%"]);
}
if ($statusF) {
    $where .= " AND status=?"; $params[] = $statusF;
}

$total = $db->prepare("SELECT COUNT(*) FROM applications WHERE $where");
$total->execute($params); $total = $total->fetchColumn();
$totalPages = ceil($total/$perPage);

$stmt = $db->prepare("SELECT * FROM applications WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute(array_merge($params, [$perPage, $offset]));
$apps = $stmt->fetchAll();

$flash = getFlash();
include __DIR__ . '/includes/layout.php';
?>

<?php if($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>"><?= $flash['type']==='success'?'✅':'❌' ?> <?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h2>📋 Arizalar</h2>
    <p>Barcha qabul arizalari ro'yxati</p>
  </div>
  <div class="flex gap-2">
    <a href="export.php?type=applications" class="btn btn-success" style="font-size:.8rem">⬇️ Excel</a>
  </div>
</div>

<!-- Filters -->
<div class="panel" style="margin-bottom:20px;padding:16px 20px">
  <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:180px">
      <label class="form-label">Qidirish</label>
      <input type="text" name="q" class="form-control" style="margin:0" placeholder="Ism, familya, telefon..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div style="min-width:150px">
      <label class="form-label">Holat</label>
      <select name="status" class="form-control" style="margin:0">
        <option value="">Barchasi</option>
        <option value="new" <?= $statusF==='new'?'selected':'' ?>>Yangi</option>
        <option value="contacted" <?= $statusF==='contacted'?'selected':'' ?>>Bog'lanildi</option>
        <option value="enrolled" <?= $statusF==='enrolled'?'selected':'' ?>>Qabul qilindi</option>
        <option value="cancelled" <?= $statusF==='cancelled'?'selected':'' ?>>Bekor</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="height:42px">🔍 Qidirish</button>
    <?php if($search||$statusF): ?>
    <a href="applications.php" class="btn btn-info" style="height:42px">✕ Tozalash</a>
    <?php endif; ?>
  </form>
</div>

<div class="panel">
  <div class="panel-head">
    <h3>Jami <?= $total ?> ta ariza</h3>
  </div>
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>#</th><th>Ism</th><th>Familya</th><th>Sinf</th>
          <th>Yo'nalish</th><th>Telefon</th><th>Holat</th><th>Sana</th><th>Amal</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($apps)): ?>
        <tr><td colspan="9" class="text-center muted" style="padding:32px">Arizalar topilmadi</td></tr>
        <?php else: foreach($apps as $i=>$a): ?>
        <tr>
          <td class="muted"><?= $offset+$i+1 ?></td>
          <td><strong><?= htmlspecialchars($a['first_name']) ?></strong></td>
          <td><?= htmlspecialchars($a['last_name']) ?></td>
          <td><?= htmlspecialchars($a['grade']) ?></td>
          <td><?= htmlspecialchars($a['direction']) ?></td>
          <td><a href="tel:<?= htmlspecialchars($a['phone']) ?>" style="color:var(--accent)"><?= htmlspecialchars($a['phone']) ?></a></td>
          <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
          <td class="muted small"><?= date('d.m.Y H:i', strtotime($a['created_at'])) ?></td>
          <td class="actions">
            <button class="btn-sm" onclick="openEdit(<?= htmlspecialchars(json_encode($a)) ?>)">✏️</button>
            <a href="?action=delete&id=<?= $a['id'] ?>" class="btn-sm" style="color:#fca5a5"
               data-confirm="Bu arizani o'chirmoqchimisiz?">🗑</a>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if($totalPages>1): ?>
<div class="pagination">
  <?php for($i=1;$i<=$totalPages;$i++): ?>
  <a href="?p=<?=$i?>&q=<?=urlencode($search)?>&status=<?=urlencode($statusF)?>"
     class="page-btn <?=$i===$page?'active':''?>"><?=$i?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-head">
      <h3>✏️ Ariza tahrirlash</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <form method="POST">
      <input type="hidden" name="update_status" value="1">
      <input type="hidden" name="id" id="editId">
      <div class="form-group">
        <label class="form-label">Holat</label>
        <select name="status" id="editStatus" class="form-control">
          <option value="new">Yangi</option>
          <option value="contacted">Bog'lanildi</option>
          <option value="enrolled">Qabul qilindi</option>
          <option value="cancelled">Bekor</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Izoh</label>
        <textarea name="note" id="editNote" class="form-control" rows="3" placeholder="Izoh qoldiring..."></textarea>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="btn btn-primary">💾 Saqlash</button>
        <button type="button" class="btn btn-info" onclick="closeModal()">Bekor</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(data){
  document.getElementById('editId').value=data.id;
  document.getElementById('editStatus').value=data.status;
  document.getElementById('editNote').value=data.note||'';
  document.getElementById('editModal').classList.add('open');
}
function closeModal(){
  document.querySelectorAll('.modal-overlay').forEach(m=>m.classList.remove('open'));
}
document.querySelectorAll('.modal-overlay').forEach(m=>{
  m.addEventListener('click',e=>{ if(e.target===m)closeModal(); });
});
</script>

<?php include __DIR__ . '/includes/layout_end.php'; ?>
