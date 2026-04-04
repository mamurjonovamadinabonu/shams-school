<?php
// =============================================
//  SHAMS School — Backend Config
// =============================================

define('DB_PATH', __DIR__ . '/../../database/shams.db');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // "password"
define('SESSION_NAME', 'shams_admin');
define('SITE_NAME', 'SHAMS Private School');
define('ADMIN_EMAIL', 'admin@shamsschool.uz');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// PDO baza ulanishi
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    try {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON;');
        $pdo->exec('PRAGMA journal_mode = WAL;');
        initDB($pdo);
    } catch (PDOException $e) {
        die('Database xatosi: ' . $e->getMessage());
    }
    return $pdo;
}

// Jadvallarni yaratish
function initDB(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS applications (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            first_name  TEXT NOT NULL,
            last_name   TEXT NOT NULL,
            grade       TEXT NOT NULL,
            direction   TEXT NOT NULL,
            phone       TEXT NOT NULL,
            status      TEXT DEFAULT 'new',
            note        TEXT DEFAULT '',
            created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS teachers (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            name        TEXT NOT NULL,
            subject     TEXT NOT NULL,
            experience  TEXT,
            bio         TEXT,
            photo       TEXT DEFAULT '',
            email       TEXT DEFAULT '',
            active      INTEGER DEFAULT 1,
            created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS news (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            title       TEXT NOT NULL,
            content     TEXT NOT NULL,
            category    TEXT DEFAULT 'General',
            image       TEXT DEFAULT '',
            published   INTEGER DEFAULT 1,
            created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS discounts (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            title       TEXT NOT NULL,
            description TEXT NOT NULL,
            percent     INTEGER DEFAULT 0,
            active      INTEGER DEFAULT 1,
            expires_at  DATE,
            created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS settings (
            key         TEXT PRIMARY KEY,
            value       TEXT NOT NULL,
            updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Namuna ma'lumotlar
    $count = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO teachers (name, subject, experience, bio) VALUES
            ('Aziza Karimova', 'Matematika', '12 yil', 'Matematika olimpiadasi g''olibi tayyorlovchi ustoz.'),
            ('Bobur Toshmatov', 'Fizika', '8 yil', 'Tajribali fizika o''qituvchisi va ilmiy tadqiqotchi.'),
            ('Dilnoza Yusupova', 'Ingliz Tili', '10 yil', 'IELTS 8.5 ball egasi, ingliz tili mutaxassisi.'),
            ('Sardor Nazarov', 'Robototexnika va IT', '6 yil', 'Robototexnika va dasturlash bo''yicha yetakchi mutaxassis.'),
            ('Malika Rahimova', 'Biologiya', '9 yil', 'Biologiya va kimyo fanlaridan tajribali pedagog.')
        ");

        $pdo->exec("
            INSERT INTO news (title, content, category) VALUES
            ('SHAMS School 2025-2026 o''quv yiliga qabul boshladi', '2025-2026 o''quv yiliga qabul arizalari qabul qilinmoqda. 1-4 sinflar uchun imtiyozlar mavjud.', 'Announcement'),
            ('Robototexnika musobaqasida birinchi o''rin', 'SHAMS maktabi o''quvchilari respublika robototexnika musobaqasida birinchi o''rinni egalladi.', 'Achievement'),
            ('Yangi Smart Darsxona ochildi', 'Maktabimizda zamonaviy texnologiyalar bilan jihozlangan yangi smart darsxona faoliyatini boshladi.', 'School News')
        ");

        $pdo->exec("
            INSERT INTO discounts (title, description, percent, expires_at) VALUES
            ('1-4 sinf imtiyozi', 'Boshlang''ich sinflarga o''quvchilar uchun maxsus chegirma', 20, '2025-09-01'),
            ('Aka-uka/Opa-singil chegirmasi', 'Ikki va undan ortiq farzand o''qisa chegirma', 25, NULL),
            ('Erta ro''yxatdan o''tish', 'May oyi oxirigacha ro''yxatdan o''tganlar uchun', 15, '2025-05-31')
        ");

        $pdo->exec("
            INSERT INTO settings (key, value) VALUES
            ('school_name', 'SHAMS Private School'),
            ('phone', '+998 71 200 30 40'),
            ('address', '123 Education Avenue, Tashkent'),
            ('telegram', '@shamsschool'),
            ('instagram', '@shams.school.official'),
            ('total_students', '1200'),
            ('total_teachers', '60'),
            ('years_experience', '15'),
            ('admission_rate', '98')
        ");
    }
}

// Auth tekshiruv
function requireAuth(): void {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: ' . getAdminRoot() . '/login.php');
        exit();
    }
}

function getAdminRoot(): string {
    return '/admin';
}

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_logged_in']);
}

// Flash xabar
function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function getFlash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// JSON javob
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
