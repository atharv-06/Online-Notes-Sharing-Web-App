<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['file'])) {
    http_response_code(403);
    exit("Unauthorized access.");
}

$filename = basename($_GET['file']); // secure the input
$filepath = __DIR__ . '/uploads/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    exit("File not found.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

if (ob_get_level()) {
    ob_end_clean();
}
flush();
readfile($filepath);
exit;
