<?php

ini_set('display_errors', 0);

$name = isset($_GET['name']) ? trim((string)$_GET['name']) : '';

if ($name === '' || str_contains($name, '..') || str_contains($name, '/') || str_contains($name, '\\')) {
    http_response_code(400);
    exit;
}

$rootPath = dirname(__DIR__);
$candidates = [
    $rootPath . '/api/files/' . $name,
    $rootPath . '/files/' . $name, // legacy uploads
];

$filePath = null;
foreach ($candidates as $candidate) {
    if (is_file($candidate)) {
        $filePath = $candidate;
        break;
    }
}

if ($filePath === null) {
    http_response_code(404);
    exit;
}

$mimeType = null;
if (class_exists('finfo')) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($filePath);
}

if (!is_string($mimeType) || $mimeType === '') {
    $mimeType = 'application/octet-stream';
}

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . (string)filesize($filePath));
header('Cache-Control: public, max-age=86400');
header('X-Content-Type-Options: nosniff');

readfile($filePath);
exit;
