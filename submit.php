<?php
// ==============================================
//  SHAMS School — Ariza qabul qilish (Backend)
// ==============================================
require_once __DIR__ . '/admin/includes/config.php';

// Faqat POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

function clean($data): string {
    return htmlspecialchars(strip_tags(trim($data)));
}

$firstName = clean($_POST['firstName'] ?? '');
$lastName  = clean($_POST['lastName'] ?? '');
$grade     = clean($_POST['grade'] ?? '');
$direction = clean($_POST['direction'] ?? '');
$phone     = clean($_POST['phone'] ?? '');

// Validatsiya
$errors = [];
if (empty($firstName)) $errors[] = "Ism kiritilmadi";
if (empty($lastName))  $errors[] = "Familya kiritilmadi";
if (empty($grade))     $errors[] = "Sinf tanlanmadi";
if (empty($direction)) $errors[] = "Yo'nalish tanlanmadi";
if (empty($phone))     $errors[] = "Telefon kiritilmadi";

if (!empty($errors)) {
    header("Location: contact.php?error=" . urlencode(implode(", ", $errors)));
    exit();
}

// Bazaga saqlash (FIREBASE API)
$appData = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'grade' => $grade,
    'direction' => $direction,
    'phone' => $phone,
    'status' => 'new',
    'note' => '',
    'created_at' => date('Y-m-d H:i:s')
];

$res = fb_request('/applications', 'POST', $appData);

header("Location: contact.php?success=1");
exit();
