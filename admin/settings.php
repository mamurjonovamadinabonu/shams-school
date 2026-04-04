<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
$db = getDB();
$pageTitle = 'Sozlamalar';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $keys = ['school_name','phone','address','telegram','instagram',
             'total_students','total_teachers','years_experience','admission_rate'];
    foreach($keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $stmt = $db->prepare("INSERT INTO settings (key, value, updated_at) VALUES (?,?,CURRENT_TIMESTAMP)
                              ON CONFLICT(key) DO UPDATE SET value=excluded.value, updated_at=CURRENT_TIMESTAMP");
        $stmt->execute([$key, $val]);
    }

    // Change password
    if (!empty($_POST['new_pass'])) {
        $hashed = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
        // In real app save to file or DB. Here just show message.
        setFlash('success', 'Parol o\'zgartirildi! config.php dagi ADMIN_PASS ni yangilang: ' . $hashed);
    } else {
        setFlash('success', 'Sozlamalar saqlandi');
    }
    header('Location: settings.php'); exit();
}

// Load settings
$settings = [];
foreach ($db->query("SELECT key, value FROM settings") as $row) {
    $settings[$row['key']] = $row['value'];
}

$flash = getFlash();
include __DIR__ . '/includes/layout.php';
?>

<?php if($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>"><?= $flash['type']==='success'?'✅':'❌' ?> <?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h2>⚙️ Sozlamalar</h2>
    <p>Sayt va maktab ma'lumotlarini sozlash</p>
  </div>
</div>

<form method="POST">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  <!-- Maktab ma'lumotlari -->
  <div class="form-panel">
    <h3 style="color:var(--white);font-size:1rem;margin-bottom:20px;display:flex;align-items:center;gap:8px">
      🏫 Maktab Ma'lumotlari
    </h3>
    <div class="form-group">
      <label class="form-label">Maktab nomi</label>
      <input type="text" name="school_name" class="form-control"
             value="<?= htmlspecialchars($settings['school_name']??'SHAMS Private School') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Telefon</label>
      <input type="text" name="phone" class="form-control"
             value="<?= htmlspecialchars($settings['phone']??'+998 71 200 30 40') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Manzil</label>
      <input type="text" name="address" class="form-control"
             value="<?= htmlspecialchars($settings['address']??'') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Telegram</label>
      <input type="text" name="telegram" class="form-control"
             value="<?= htmlspecialchars($settings['telegram']??'@shamsschool') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Instagram</label>
      <input type="text" name="instagram" class="form-control"
             value="<?= htmlspecialchars($settings['instagram']??'') ?>">
    </div>
  </div>

  <!-- Statistikalar -->
  <div>
    <div class="form-panel" style="margin-bottom:20px">
      <h3 style="color:var(--white);font-size:1rem;margin-bottom:20px;display:flex;align-items:center;gap:8px">
        📊 Statistikalar (Saytda ko'rsatiladi)
      </h3>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">O'quvchilar soni</label>
          <input type="text" name="total_students" class="form-control"
                 value="<?= htmlspecialchars($settings['total_students']??'1200') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">O'qituvchilar soni</label>
          <input type="text" name="total_teachers" class="form-control"
                 value="<?= htmlspecialchars($settings['total_teachers']??'60') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tajriba yillari</label>
          <input type="text" name="years_experience" class="form-control"
                 value="<?= htmlspecialchars($settings['years_experience']??'15') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Qabul darajasi (%)</label>
          <input type="text" name="admission_rate" class="form-control"
                 value="<?= htmlspecialchars($settings['admission_rate']??'98') ?>">
        </div>
      </div>
    </div>

    <!-- Parol o'zgartirish -->
    <div class="form-panel">
      <h3 style="color:var(--white);font-size:1rem;margin-bottom:20px;display:flex;align-items:center;gap:8px">
        🔐 Parolni O'zgartirish
      </h3>
      <div class="form-group">
        <label class="form-label">Yangi parol</label>
        <input type="password" name="new_pass" class="form-control" placeholder="Bo'sh qoldiring — o'zgarmaydi">
      </div>
      <div class="form-group">
        <label class="form-label">Tasdiqlash</label>
        <input type="password" name="confirm_pass" class="form-control" placeholder="Yangi parolni tasdiqlang">
      </div>
      <div style="background:rgba(245,166,35,0.08);border:1px solid rgba(245,166,35,0.2);border-radius:8px;padding:10px 14px;font-size:0.78rem;color:rgba(255,255,255,0.5)">
        ⚠️ Parolni o'zgartirgandan so'ng <code style="color:var(--accent)">config.php</code> ga yangi hash kodni ko'chiring.
      </div>
    </div>
  </div>
</div>

<div style="margin-top:20px">
  <button type="submit" class="btn btn-primary" style="padding:13px 32px;font-size:1rem">
    💾 Sozlamalarni Saqlash
  </button>
</div>
</form>

<?php include __DIR__ . '/includes/layout_end.php'; ?>
