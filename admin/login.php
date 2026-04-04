<?php
require_once __DIR__ . '/includes/config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $user;
        header('Location: index.php');
        exit();
    } else {
        $error = "Login yoki parol noto'g'ri!";
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login — SHAMS School</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0C2B4E;--primary-light:#1a4a7e;--accent:#F5A623;
  --white:#ffffff;--text:#1e293b;--muted:#64748b;
  --danger:#ef4444;--success:#22c55e;
  --bg:#0a1628;--card:rgba(255,255,255,0.04);
  --border:rgba(255,255,255,0.08);
}
body{
  font-family:'Inter',sans-serif;
  background:var(--bg);
  min-height:100vh;display:flex;align-items:center;justify-content:center;
  position:relative;overflow:hidden;
}
/* Animated BG */
body::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(ellipse 60% 50% at 20% 20%, rgba(12,43,78,0.8) 0%, transparent 60%),
    radial-gradient(ellipse 40% 40% at 80% 80%, rgba(245,166,35,0.15) 0%, transparent 50%),
    radial-gradient(ellipse 50% 60% at 50% 50%, rgba(26,74,126,0.3) 0%, transparent 70%);
}
.particles{position:absolute;inset:0;overflow:hidden;pointer-events:none}
.particle{
  position:absolute;border-radius:50%;
  background:rgba(245,166,35,0.15);
  animation:float linear infinite;
}
@keyframes float{
  0%{transform:translateY(100vh) rotate(0deg);opacity:0}
  10%{opacity:1}90%{opacity:1}
  100%{transform:translateY(-100px) rotate(720deg);opacity:0}
}
.login-wrap{
  position:relative;z-index:10;
  width:100%;max-width:440px;padding:20px;
}
.login-logo{text-align:center;margin-bottom:32px}
.logo-icon{
  width:72px;height:72px;border-radius:20px;
  background:linear-gradient(135deg,var(--primary),var(--primary-light));
  display:flex;align-items:center;justify-content:center;
  font-size:1.8rem;font-weight:900;color:var(--accent);
  margin:0 auto 16px;
  box-shadow:0 8px 32px rgba(12,43,78,0.6),0 0 0 1px rgba(245,166,35,0.2);
  letter-spacing:-1px;
}
.login-logo h1{
  font-size:1.5rem;font-weight:800;color:var(--white);
  letter-spacing:-0.5px;
}
.login-logo p{font-size:0.85rem;color:var(--muted);margin-top:4px}

.card{
  background:rgba(255,255,255,0.04);
  border:1px solid var(--border);
  border-radius:20px;padding:40px;
  backdrop-filter:blur(20px);
  box-shadow:0 24px 64px rgba(0,0,0,0.4);
}
.form-group{margin-bottom:20px}
.form-label{
  display:block;font-size:0.8rem;font-weight:600;
  color:rgba(255,255,255,0.6);margin-bottom:8px;
  text-transform:uppercase;letter-spacing:1px;
}
.form-control{
  width:100%;padding:14px 18px;
  background:rgba(255,255,255,0.06);
  border:1px solid var(--border);
  border-radius:12px;
  color:var(--white);font-size:0.95rem;font-family:'Inter',sans-serif;
  transition:all .3s ease;outline:none;
}
.form-control::placeholder{color:rgba(255,255,255,0.25)}
.form-control:focus{
  border-color:var(--accent);
  background:rgba(255,255,255,0.09);
  box-shadow:0 0 0 3px rgba(245,166,35,0.12);
}
.btn-login{
  width:100%;padding:15px;
  background:linear-gradient(135deg,var(--accent) 0%,#e09520 100%);
  color:var(--primary);font-size:1rem;font-weight:700;
  border:none;border-radius:12px;cursor:pointer;
  transition:all .3s ease;font-family:'Inter',sans-serif;
  position:relative;overflow:hidden;margin-top:8px;
}
.btn-login::after{
  content:'';position:absolute;inset:0;
  background:rgba(255,255,255,0);
  transition:background .3s;
}
.btn-login:hover::after{background:rgba(255,255,255,0.12)}
.btn-login:active{transform:scale(0.98)}
.alert{
  padding:12px 16px;border-radius:10px;
  font-size:0.88rem;margin-bottom:20px;
  display:flex;align-items:center;gap:8px;
}
.alert-danger{background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#fca5a5}
.divider{
  display:flex;align-items:center;gap:12px;
  margin:24px 0 20px;
}
.divider::before,.divider::after{
  content:'';flex:1;height:1px;background:var(--border);
}
.divider span{font-size:0.75rem;color:var(--muted)}
.hint{
  background:rgba(245,166,35,0.06);
  border:1px solid rgba(245,166,35,0.15);
  border-radius:10px;padding:12px 16px;
  font-size:0.8rem;color:rgba(255,255,255,0.5);
  text-align:center;
}
.hint strong{color:rgba(245,166,35,0.8)}
.input-icon-wrap{position:relative}
.input-icon{
  position:absolute;right:16px;top:50%;transform:translateY(-50%);
  font-size:1rem;opacity:0.4;cursor:pointer;
  color:var(--white);user-select:none;
}
</style>
</head>
<body>
<div class="particles" id="particles"></div>

<div class="login-wrap">
  <div class="login-logo">
    <div class="logo-icon">S</div>
    <h1>SHAMS Admin</h1>
    <p>Boshqaruv paneliga kirish</p>
  </div>

  <div class="card">
    <?php if($error): ?>
    <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label class="form-label" for="username">Foydalanuvchi nomi</label>
        <div class="input-icon-wrap">
          <input type="text" class="form-control" id="username" name="username"
                 placeholder="admin" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 autocomplete="username" required>
          <span class="input-icon">👤</span>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Parol</label>
        <div class="input-icon-wrap">
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="••••••••" autocomplete="current-password" required>
          <span class="input-icon" onclick="togglePass()" title="Ko'rsatish">👁</span>
        </div>
      </div>
      <button type="submit" class="btn-login">🔐 Kirish</button>
    </form>

    <div class="divider"><span>Demo ma'lumotlar</span></div>
    <div class="hint">
      Login: <strong>admin</strong> &nbsp;|&nbsp; Parol: <strong>password</strong>
    </div>
  </div>
</div>

<script>
// Particles
const container = document.getElementById('particles');
for (let i = 0; i < 20; i++) {
  const p = document.createElement('div');
  p.className = 'particle';
  const size = Math.random() * 8 + 3;
  p.style.cssText = `
    width:${size}px;height:${size}px;
    left:${Math.random()*100}%;
    animation-duration:${Math.random()*15+10}s;
    animation-delay:${Math.random()*10}s;
    opacity:${Math.random()*0.5};
  `;
  container.appendChild(p);
}

function togglePass() {
  const p = document.getElementById('password');
  p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
