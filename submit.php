<?php
// ==============================================
//  SHAMS School — Ariza qabul qilish (Backend)
// ==============================================
require_once __DIR__ . '/panel/includes/config.php';

// Faqat POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
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
    header("Location: contact.html?error=" . urlencode(implode(", ", $errors)));
    exit();
}

// Bazaga saqlash
try {
    $db = getDB();
    $stmt = $db->prepare(
        "INSERT INTO applications (first_name, last_name, grade, direction, phone)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$firstName, $lastName, $grade, $direction, $phone]);
} catch (Exception $e) {
    // Fallback: faylga yozish
    $logData = date('Y-m-d H:i:s') . " | $firstName | $lastName | $grade | $direction | $phone\n";
    file_put_contents(__DIR__ . '/database/submissions.txt', $logData, FILE_APPEND | LOCK_EX);
}

// Email (ixtiyoriy)
$to      = ADMIN_EMAIL;
$subject = "Yangi ariza: $firstName $lastName";
$message = "Ism: $firstName $lastName\nSinf: $grade\nYo'nalish: $direction\nTelefon: $phone";
$headers = "From: noreply@shamsschool.uz";
@mail($to, $subject, $message, $headers);

header("Location: contact.html?success=1");
exit();
