<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
$db = getDB();
$pageTitle = "O'qituvchilar";

$action = $_GET['action'] ?? '';
$editData = null;

// Rasm yuklash funksiyasi
function uploadPhoto(string $field, string $oldPhoto = ''): string {
    if (empty($_FILES[$field]['name'])) return $oldPhoto;
    
    $uploadDir = __DIR__ . '/../images/teachers/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $file = $_FILES[$field];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    
    if (!in_array($ext, $allowed)) return $oldPhoto;
    if ($file['size'] > 5 * 1024 * 1024) return $oldPhoto; // 5MB limit
    
    $newName = 'teacher_' . time() . '_' . rand(100,999) . '.' . $ext;
    $dest = $uploadDir . $newName;
    
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        // Eski rasmni o'chirish
        if ($oldPhoto && strpos($oldPhoto, 'teachers/') !== false) {
            $oldPath = __DIR__ . '/../images/' . basename(dirname($oldPhoto)) . '/' . basename($oldPhoto);
            if (file_exists($oldPath)) @unlink($oldPath);
        }
        return 'images/teachers/' . $newName;
    }
    return $oldPhoto;
}

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $t = $db->prepare("SELECT photo FROM teachers WHERE id=?");
    $t->execute([$_GET['id']]);
    $row = $t->fetch();
    if ($row && $row['photo']) {
        $p = __DIR__ . '/../' . $row['photo'];
        if (file_exists($p)) @unlink($p);
    }
    $db->prepare("DELETE FROM teachers WHERE id=?")->execute([$_GET['id']]);
    setFlash('success', "O'qituvchi o'chirildi");
    header('Location: teachers.php'); exit();
}

// SAVE (add/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = (int)($_POST['id'] ?? 0);
    $name    = trim($_POST['name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $exp     = trim($_POST['experience'] ?? '');
    $bio     = trim($_POST['bio'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $active  = isset($_POST['active']) ? 1 : 0;

    if ($id > 0) {
        $old = $db->prepare("SELECT photo FROM teachers WHERE id=?");
        $old->execute([$id]);
        $oldRow = $old->fetch();
        $photo = uploadPhoto('photo', $oldRow['photo'] ?? '');
        $db->prepare("UPDATE teachers SET name=?,subject=?,experience=?,bio=?,email=?,active=?,photo=? WHERE id=?")
           ->execute([$name,$subject,$exp,$bio,$email,$active,$photo,$id]);
        setFlash('success', "O'qituvchi yangilandi");
    } else {
        $photo = uploadPhoto('photo');
        $db->prepare("INSERT INTO teachers (name,subject,experience,bio,email,active,photo) VALUES(?,?,?,?,?,?,?)")
           ->execute([$name,$subject,$exp,$bio,$email,$active,$photo]);
        setFlash('success', "O'qituvchi qo'shildi");
    }
    header('Location: teachers.php'); exit();
}

// EDIT LOAD
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM teachers WHERE id=?");
    $stmt->execute([$_GET['id']]);
    $editData = $stmt->fetch();
}

$teachers = $db->query("SELECT * FROM teachers ORDER BY id DESC")->fetchAll();
$flash = getFlash();
include __DIR__ . '/includes/layout.php';
?>

<?php if($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>"><?= $flash['type']==='success'?'✅':'❌' ?> <?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h2>👨‍🏫 O'qituvchilar</h2>
    <p>O'qituvchilar ro'yxatini boshqarish</p>
  </div>
  <button class="btn btn-primary" onclick="openAddModal()">+ Qo'shish</button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px">
  <?php foreach($teachers as $t): ?>
  <div class="panel" style="padding:0;overflow:hidden">
    <div style="background:linear-gradient(135deg,var(--primary),var(--primary-light));padding:20px;display:flex;align-items:center;gap:14px">
      <?php if($t['photo']): ?>
      <img src="<?= htmlspecialchars('../' . $t['photo']) ?>"
           style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);flex-shrink:0" alt="">
      <?php else: ?>
      <div style="width:52px;height:52px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:900;color:var(--primary-dark);flex-shrink:0">
        <?= mb_substr($t['name'],0,1) ?>
      </div>
      <?php endif; ?>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;color:white;font-size:0.95rem"><?= htmlspecialchars($t['name']) ?></div>
        <div style="font-size:0.78rem;color:rgba(255,255,255,0.6)"><?= htmlspecialchars($t['subject']) ?></div>
      </div>
      <span class="badge badge-<?= $t['active'] ?>"><?= $t['active']?'Faol':'Nofaol' ?></span>
    </div>
    <div style="padding:16px 20px">
      <?php if($t['experience']): ?>
      <div style="font-size:0.8rem;color:var(--muted);margin-bottom:6px">🕐 Tajriba: <strong style="color:var(--text)"><?= htmlspecialchars($t['experience']) ?></strong></div>
      <?php endif; ?>
      <?php if($t['email']): ?>
      <div style="font-size:0.8rem;color:var(--muted);margin-bottom:6px">📧 <?= htmlspecialchars($t['email']) ?></div>
      <?php endif; ?>
      <?php if($t['bio']): ?>
      <p style="font-size:0.82rem;color:var(--muted);line-height:1.5;margin-bottom:12px"><?= htmlspecialchars(mb_substr($t['bio'],0,80)) ?>...</p>
      <?php endif; ?>
      <div class="flex gap-2">
        <button class="btn-sm" onclick='openEditModal(<?= htmlspecialchars(json_encode($t)) ?>)'>✏️ Tahrirlash</button>
        <a href="?action=delete&id=<?= $t['id'] ?>" class="btn-sm" style="color:#fca5a5"
           data-confirm="O'qituvchini o'chirmoqchimisiz?">🗑 O'chirish</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="teacherModal">
  <div class="modal" style="max-width:600px">
    <div class="modal-head">
      <h3 id="modalTitle">👨‍🏫 O'qituvchi qo'shish</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="tId" value="0">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Ism Familya *</label>
          <input type="text" name="name" id="tName" class="form-control" required placeholder="Aziza Karimova">
        </div>
        <div class="form-group">
          <label class="form-label">Fan *</label>
          <input type="text" name="subject" id="tSubject" class="form-control" required placeholder="Matematika">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tajriba</label>
          <input type="text" name="experience" id="tExp" class="form-control" placeholder="12 yil">
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" id="tEmail" class="form-control" placeholder="email@school.uz">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Bio</label>
        <textarea name="bio" id="tBio" class="form-control" rows="3" placeholder="Qisqacha ma'lumot..."></textarea>
      </div>

      <!-- RASM YUKLASH -->
      <div class="form-group">
        <label class="form-label">📸 Rasm (JPG, PNG, WEBP — max 5MB)</label>
        <div style="border:2px dashed var(--border);border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:all .2s"
             id="photoDropZone" onclick="document.getElementById('photoFile').click()">
          <div id="photoPreviewWrap">
            <img id="photoPreview" src="" alt="" style="display:none;max-height:120px;border-radius:8px;margin:0 auto 8px">
            <p id="photoLabel" style="color:var(--muted);font-size:0.85rem">🖼️ Rasm tanlash uchun bosing</p>
          </div>
        </div>
        <input type="file" name="photo" id="photoFile" accept="image/*" style="display:none" onchange="previewPhoto(this)">
        <p id="currentPhotoInfo" style="font-size:0.78rem;color:var(--muted);margin-top:6px;display:none">Hozirgi rasm: <span id="currentPhotoName"></span></p>
      </div>

      <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px">
        <input type="checkbox" name="active" id="tActive" value="1" checked style="width:auto">
        <label for="tActive" style="font-size:0.875rem;color:var(--text)">Faol holat</label>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="btn btn-primary">💾 Saqlash</button>
        <button type="button" class="btn btn-info" onclick="closeModal()">Bekor</button>
      </div>
    </form>
  </div>
</div>

<script>
function previewPhoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('photoPreview').src = e.target.result;
      document.getElementById('photoPreview').style.display = 'block';
      document.getElementById('photoLabel').textContent = '✅ ' + input.files[0].name;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
function openAddModal(){
  document.getElementById('modalTitle').textContent="👨‍🏫 O'qituvchi qo'shish";
  document.getElementById('tId').value=0;
  ['tName','tSubject','tExp','tEmail','tBio'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('tActive').checked=true;
  document.getElementById('photoPreview').style.display='none';
  document.getElementById('photoLabel').textContent='🖼️ Rasm tanlash uchun bosing';
  document.getElementById('photoFile').value='';
  document.getElementById('currentPhotoInfo').style.display='none';
  document.getElementById('teacherModal').classList.add('open');
}
function openEditModal(d){
  document.getElementById('modalTitle').textContent="✏️ O'qituvchini tahrirlash";
  document.getElementById('tId').value=d.id;
  document.getElementById('tName').value=d.name||'';
  document.getElementById('tSubject').value=d.subject||'';
  document.getElementById('tExp').value=d.experience||'';
  document.getElementById('tEmail').value=d.email||'';
  document.getElementById('tBio').value=d.bio||'';
  document.getElementById('tActive').checked=d.active==1;
  document.getElementById('photoFile').value='';
  if (d.photo) {
    document.getElementById('photoPreview').src = '../' + d.photo;
    document.getElementById('photoPreview').style.display='block';
    document.getElementById('photoLabel').textContent='🖼️ Yangi rasm tanlash (ixtiyoriy)';
    document.getElementById('currentPhotoInfo').style.display='block';
    document.getElementById('currentPhotoName').textContent=d.photo;
  } else {
    document.getElementById('photoPreview').style.display='none';
    document.getElementById('photoLabel').textContent='🖼️ Rasm tanlash uchun bosing';
    document.getElementById('currentPhotoInfo').style.display='none';
  }
  document.getElementById('teacherModal').classList.add('open');
}
function closeModal(){
  document.querySelectorAll('.modal-overlay').forEach(m=>m.classList.remove('open'));
}
document.querySelectorAll('.modal-overlay').forEach(m=>{
  m.addEventListener('click',e=>{ if(e.target===m)closeModal(); });
});
document.querySelectorAll('[data-confirm]').forEach(el=>{
  el.addEventListener('click',e=>{
    if(!confirm(el.dataset.confirm)) e.preventDefault();
  });
});
</script>

<?php include __DIR__ . '/includes/layout_end.php'; ?>
