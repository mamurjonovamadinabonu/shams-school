<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
$db = getDB();
$pageTitle = 'Yangiliklar';

$action = $_GET['action'] ?? '';

// Rasm yuklash funksiyasi
function uploadNewsImage(string $field, string $oldImage = ''): string {
    if (empty($_FILES[$field]['name'])) return $oldImage;

    $uploadDir = __DIR__ . '/../images/news/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $file = $_FILES[$field];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];

    if (!in_array($ext, $allowed)) return $oldImage;
    if ($file['size'] > 8 * 1024 * 1024) return $oldImage; // 8MB limit

    $newName = 'news_' . time() . '_' . rand(100,999) . '.' . $ext;
    $dest = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        if ($oldImage && strpos($oldImage, 'news/') !== false) {
            $oldPath = __DIR__ . '/../images/news/' . basename($oldImage);
            if (file_exists($oldPath)) @unlink($oldPath);
        }
        return 'images/news/' . $newName;
    }
    return $oldImage;
}

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $row = $db->prepare("SELECT image FROM news WHERE id=?");
    $row->execute([$_GET['id']]);
    $n = $row->fetch();
    if ($n && $n['image']) {
        $p = __DIR__ . '/../images/news/' . basename($n['image']);
        if (file_exists($p)) @unlink($p);
    }
    $db->prepare("DELETE FROM news WHERE id=?")->execute([$_GET['id']]);
    setFlash('success', 'Yangilik o\'chirildi');
    header('Location: news.php'); exit();
}

// TOGGLE published
if ($action === 'toggle' && isset($_GET['id'])) {
    $db->prepare("UPDATE news SET published = CASE WHEN published=1 THEN 0 ELSE 1 END WHERE id=?")->execute([$_GET['id']]);
    header('Location: news.php'); exit();
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)($_POST['id'] ?? 0);
    $title     = trim($_POST['title'] ?? '');
    $content   = trim($_POST['content'] ?? '');
    $category  = trim($_POST['category'] ?? 'General');
    $published = isset($_POST['published']) ? 1 : 0;

    if ($id > 0) {
        $old = $db->prepare("SELECT image FROM news WHERE id=?");
        $old->execute([$id]);
        $oldRow = $old->fetch();
        $image = uploadNewsImage('image', $oldRow['image'] ?? '');
        $db->prepare("UPDATE news SET title=?,content=?,category=?,published=?,image=? WHERE id=?")
           ->execute([$title,$content,$category,$published,$image,$id]);
        setFlash('success','Yangilik yangilandi');
    } else {
        $image = uploadNewsImage('image');
        $db->prepare("INSERT INTO news (title,content,category,published,image) VALUES (?,?,?,?,?)")
           ->execute([$title,$content,$category,$published,$image]);
        setFlash('success',"Yangilik qo'shildi");
    }
    header('Location: news.php'); exit();
}

$newsList = $db->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
$flash = getFlash();
include __DIR__ . '/includes/layout.php';
?>

<?php if($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>"><?= $flash['type']==='success'?'✅':'❌' ?> <?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h2>📰 Yangiliklar</h2>
    <p>Maktab yangiliklar va e'lonlarini boshqarish</p>
  </div>
  <button class="btn btn-primary" onclick="openAddModal()">+ Yangilik qo'shish</button>
</div>

<div class="panel">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr><th>#</th><th>Rasm</th><th>Sarlavha</th><th>Kategoriya</th><th>Holat</th><th>Sana</th><th>Amal</th></tr>
      </thead>
      <tbody>
        <?php if(empty($newsList)): ?>
        <tr><td colspan="7" class="text-center muted" style="padding:32px">Yangiliklar mavjud emas</td></tr>
        <?php else: foreach($newsList as $i=>$n): ?>
        <tr>
          <td class="muted"><?= $i+1 ?></td>
          <td>
            <?php if($n['image']): ?>
            <img src="../<?= htmlspecialchars($n['image']) ?>" style="width:56px;height:40px;object-fit:cover;border-radius:6px" alt="">
            <?php else: ?>
            <div style="width:56px;height:40px;background:var(--card);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:1.2rem">📷</div>
            <?php endif; ?>
          </td>
          <td>
            <strong><?= htmlspecialchars($n['title']) ?></strong>
            <div class="small muted" style="margin-top:2px"><?= htmlspecialchars(mb_substr($n['content'],0,60)) ?>...</div>
          </td>
          <td>
            <span style="background:rgba(245,166,35,0.12);color:var(--accent);padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700">
              <?= htmlspecialchars($n['category']) ?>
            </span>
          </td>
          <td>
            <a href="?action=toggle&id=<?= $n['id'] ?>" style="text-decoration:none">
              <span class="badge badge-<?= $n['published'] ?>"><?= $n['published']?'Chop etilgan':'Yashirin' ?></span>
            </a>
          </td>
          <td class="muted small"><?= date('d.m.Y', strtotime($n['created_at'])) ?></td>
          <td class="actions">
            <button class="btn-sm" onclick='openEditModal(<?= htmlspecialchars(json_encode($n)) ?>)'>✏️</button>
            <a href="?action=delete&id=<?= $n['id'] ?>" class="btn-sm" style="color:#fca5a5"
               data-confirm="Yangilikni o'chirmoqchimisiz?">🗑</a>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="newsModal">
  <div class="modal" style="max-width:660px">
    <div class="modal-head">
      <h3 id="nModalTitle">📰 Yangilik qo'shish</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="nId" value="0">
      <div class="form-group">
        <label class="form-label">Sarlavha *</label>
        <input type="text" name="title" id="nTitle" class="form-control" required placeholder="Yangilik sarlavhasi...">
      </div>
      <div class="form-group">
        <label class="form-label">Kontent *</label>
        <textarea name="content" id="nContent" class="form-control" rows="5" required placeholder="Yangilik matni..."></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategoriya</label>
          <select name="category" id="nCategory" class="form-control">
            <option value="Announcement">E'lon</option>
            <option value="Achievement">Yutuq</option>
            <option value="School News">Maktab yangiligi</option>
            <option value="Event">Tadbir</option>
            <option value="General">Umumiy</option>
          </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="published" id="nPublished" value="1" checked style="width:auto">
            <span style="font-size:0.875rem">Chop etish</span>
          </label>
        </div>
      </div>

      <!-- RASM YUKLASH -->
      <div class="form-group">
        <label class="form-label">📸 Yangilik rasmi (JPG, PNG, WEBP — max 8MB)</label>
        <div style="border:2px dashed var(--border);border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:all .2s"
             onclick="document.getElementById('newsImageFile').click()">
          <img id="nImgPreview" src="" alt="" style="display:none;max-height:140px;width:100%;object-fit:cover;border-radius:8px;margin-bottom:8px">
          <p id="nImgLabel" style="color:var(--muted);font-size:0.85rem">🖼️ Rasm tanlash uchun bosing</p>
        </div>
        <input type="file" name="image" id="newsImageFile" accept="image/*" style="display:none" onchange="previewNewsImg(this)">
      </div>

      <div class="flex gap-2">
        <button type="submit" class="btn btn-primary">💾 Saqlash</button>
        <button type="button" class="btn btn-info" onclick="closeModal()">Bekor</button>
      </div>
    </form>
  </div>
</div>

<script>
function previewNewsImg(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('nImgPreview').src = e.target.result;
      document.getElementById('nImgPreview').style.display = 'block';
      document.getElementById('nImgLabel').textContent = '✅ ' + input.files[0].name;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
function openAddModal(){
  document.getElementById('nModalTitle').textContent="📰 Yangilik qo'shish";
  document.getElementById('nId').value=0;
  ['nTitle','nContent'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('nCategory').value='General';
  document.getElementById('nPublished').checked=true;
  document.getElementById('nImgPreview').style.display='none';
  document.getElementById('nImgLabel').textContent='🖼️ Rasm tanlash uchun bosing';
  document.getElementById('newsImageFile').value='';
  document.getElementById('newsModal').classList.add('open');
}
function openEditModal(d){
  document.getElementById('nModalTitle').textContent="✏️ Yangilikni tahrirlash";
  document.getElementById('nId').value=d.id;
  document.getElementById('nTitle').value=d.title||'';
  document.getElementById('nContent').value=d.content||'';
  document.getElementById('nCategory').value=d.category||'General';
  document.getElementById('nPublished').checked=d.published==1;
  document.getElementById('newsImageFile').value='';
  if (d.image) {
    document.getElementById('nImgPreview').src = '../' + d.image;
    document.getElementById('nImgPreview').style.display = 'block';
    document.getElementById('nImgLabel').textContent = '🖼️ Yangi rasm tanlash (ixtiyoriy)';
  } else {
    document.getElementById('nImgPreview').style.display = 'none';
    document.getElementById('nImgLabel').textContent = '🖼️ Rasm tanlash uchun bosing';
  }
  document.getElementById('newsModal').classList.add('open');
}
function closeModal(){
  document.querySelectorAll('.modal-overlay').forEach(m=>m.classList.remove('open'));
}
document.querySelectorAll('.modal-overlay').forEach(m=>{
  m.addEventListener('click',e=>{ if(e.target===m)closeModal(); });
});
document.querySelectorAll('[data-confirm]').forEach(el=>{
  el.addEventListener('click',e=>{ if(!confirm(el.dataset.confirm)) e.preventDefault(); });
});
</script>

<?php include __DIR__ . '/includes/layout_end.php'; ?>
