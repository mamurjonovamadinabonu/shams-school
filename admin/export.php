<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();

$type = $_GET['type'] ?? 'applications';
$db = getDB();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$type.'_'.date('Y-m-d').'.csv"');
echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel

$out = fopen('php://output', 'w');

if ($type === 'applications') {
    fputcsv($out, ['#','Ism','Familya','Sinf',"Yo'nalish",'Telefon','Holat','Sana'], ';');
    $rows = $db->query("SELECT * FROM applications ORDER BY created_at DESC")->fetchAll();
    foreach($rows as $i=>$r) {
        fputcsv($out, [
            $i+1, $r['first_name'], $r['last_name'],
            $r['grade'], $r['direction'], $r['phone'],
            $r['status'], $r['created_at']
        ], ';');
    }
}

fclose($out);
exit();
