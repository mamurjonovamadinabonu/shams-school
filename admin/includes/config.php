<?php
// =============================================
//  SHAMS School — Backend Config (FIREBASE)
// =============================================

define('FIREBASE_URL', 'https://shams-school-84b9f-default-rtdb.firebaseio.com/');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // "password"
define('SESSION_NAME', 'shams_admin');
define('SITE_NAME', 'SHAMS Private School');
define('ADMIN_EMAIL', 'admin@shamsschool.uz');

// Session
if (session_status() === PHP_SESSION_NONE) {
    @session_name(SESSION_NAME);
    @session_start();
}

/**
 * Firebase bilan ishlash uchun stream stream_context (CURL ishlamasa Vercelda ishlaydi)
 */
function fb_request($path, $method = 'GET', $data = null) {
    $url = FIREBASE_URL . ltrim($path, '/') . '.json';
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => $method,
            'ignore_errors' => true,
            'timeout' => 5
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    if ($data !== null) {
        $options['http']['content'] = json_encode($data);
    }
    
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    return json_decode($result ?: '{}', true);
}

// Boshlang'ich datalarni Firebase'ga yuklash
function initFirebase() {
    $teachers = fb_request('/teachers');
    if (empty($teachers)) {
        $sampleTeachers = [
            't1' => ['name' => 'Asrorjon Homidjonov', 'subject' => 'Ingliz tili - IELTS', 'experience' => '', 'bio' => 'Grammatika, IELTS/CEFR/SAT, DTM', 'photo' => 'https://ui-avatars.com/api/?name=Asrorjon+Homidjonov&background=e3f2fd&color=0c2b4e&size=200', 'active' => 1, 'created_at' => date('Y-m-d H:i:s')]
        ];
        fb_request('/teachers', 'PUT', $sampleTeachers);
    }
    
    $settings = fb_request('/settings');
    if (empty($settings)) {
        $sampleSettings = [
            'school_name' => 'SHAMS Private School',
            'phone' => '+998 91 691 6699',
            'address' => 'Farg\'ona viloyati, Bag\'dod tumani',
            'telegram' => '@shamsschool',
            'instagram' => '@shams.school.official',
            'total_students' => '1200',
            'total_teachers' => '18',
            'years_experience' => '15',
            'admission_rate' => '98'
        ];
        fb_request('/settings', 'PUT', $sampleSettings);
    }
}

// Eskicha PDO moslashuvidan xalos qilish
function getDB() { return null; } 
function initDB($pdo) { initFirebase(); }

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
