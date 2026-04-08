<?php
// =============================================
//  SHAMS School — Backend Config (FIREBASE)
// =============================================

define('FIREBASE_URL', 'https://shams-school-84b9f-default-rtdb.firebaseio.com/'); // TO DO: Update this if region is different!
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

/**
 * Firebase bilan ishlash uchun asosiy funksiya (cURL orqali)
 */
function fb_request($path, $method = 'GET', $data = null) {
    $url = FIREBASE_URL . ltrim($path, '/') . '.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // TIZIM QOTIB QOLMASLIGI UCHUN!
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        if ($data !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $resData = json_decode($response ?: '{}', true);
    return $resData;
}

// Boshlang'ich datalarni Firebase'ga yuklash
function initFirebase() {
    $teachers = fb_request('/teachers');
    // Agar baza bo'm-bo'sh bo'lsa (yangi API)
    if (empty($teachers)) {
        $sampleTeachers = [
            't1' => ['name' => 'Asrorjon Homidjonov', 'subject' => 'Ingliz tili - IELTS', 'experience' => '', 'bio' => 'Grammatika, IELTS/CEFR/SAT, DTM', 'photo' => 'https://ui-avatars.com/api/?name=Asrorjon+Homidjonov&background=e3f2fd&color=0c2b4e&size=200', 'active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            't2' => ['name' => 'Farhodjon Ahmedov', 'subject' => 'Ingliz tili', 'experience' => '', 'bio' => 'CEFR va IELTS, DTM, Grammatika', 'photo' => 'https://ui-avatars.com/api/?name=Farhodjon+Ahmedov&background=e3f2fd&color=0c2b4e&size=200', 'active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            't3' => ['name' => 'Shaxzodaxon Xamroliyeva', 'subject' => 'Ingliz tili', 'experience' => '', 'bio' => 'Grammatika, DTM, Milliy sertifikat', 'photo' => 'https://ui-avatars.com/api/?name=Shaxzodaxon+Xamroliyeva&background=e3f2fd&color=0c2b4e&size=200', 'active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            't4' => ['name' => 'Jamshidbek G\'ulomqodirov', 'subject' => 'Matematika', 'experience' => '', 'bio' => 'Matematika, DTM, Milliy sertifikat', 'photo' => 'https://ui-avatars.com/api/?name=Jamshidbek+G\'ulomqodirov&background=e3f2fd&color=0c2b4e&size=200', 'active' => 1, 'created_at' => date('Y-m-d H:i:s')]
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
