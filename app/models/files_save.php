<?php
const UPLOAD_DIRECTORY = 'api/files/';
const LEGACY_UPLOAD_DIRECTORY = 'files/';
const ALLOWED_UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'xls', 'xlsx', 'mp4', 'webm', 'ogg', 'mov'];
const ALLOWED_IMAGE_MIME_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];
const ALLOWED_MEDIA_MIME_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'video/mp4' => 'mp4',
    'video/webm' => 'webm',
    'video/ogg' => 'ogg',
    'video/quicktime' => 'mov',
];

function generateUUID(){
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return bin2hex($data);
}

function normalizeUploadedExtension(string $fileName): ?string
{
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($extension, ALLOWED_UPLOAD_EXTENSIONS, true)) {
        return null;
    }

    return $extension;
}

function getUploadedFileTmpPath(): ?string
{
    if (!isset($_FILES['file']) || $_FILES['file']['tmp_name'] === '') {
        return null;
    }

    return $_FILES['file']['tmp_name'];
}

function getUploadPath(string $fileName): string
{
    return UPLOAD_DIRECTORY . $fileName;
}

function resolveStoredImageSrc(?string $storedValue, string $defaultSrc, string $basePath = UPLOAD_DIRECTORY): string
{
    return resolveStoredFileSrc($storedValue, $defaultSrc);
}

function resolveStoredFileSrc(?string $storedValue, string $defaultSrc): string
{
    $storedValue = trim((string) $storedValue);

    if ($storedValue === '') {
        return $defaultSrc;
    }

    if (str_starts_with($storedValue, 'admin/') || str_starts_with($storedValue, 'assets/')) {
        return $storedValue;
    }

    if (preg_match('~^https?://~i', $storedValue) === 1 || str_starts_with($storedValue, '//') || str_starts_with($storedValue, 'data:')) {
        return $storedValue;
    }

    $normalized = str_replace('\\', '/', $storedValue);
    $normalized = preg_replace('~^.*?api/files/~i', '', $normalized);
    $normalized = preg_replace('~^.*?files/~i', '', $normalized);
    $normalized = ltrim((string) $normalized, '/');

    if ($normalized === '' || str_contains($normalized, '..') || str_contains($normalized, '/')) {
        return $defaultSrc;
    }

    $rootPath = dirname(__DIR__, 2);
    $candidates = [
        $rootPath . '/' . UPLOAD_DIRECTORY . $normalized,
        $rootPath . '/' . LEGACY_UPLOAD_DIRECTORY . $normalized,
    ];

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            return 'api/file.php?name=' . rawurlencode($normalized);
        }
    }

    return $defaultSrc;
}

function saveFile() : string | null
    {
        // Retourne le nom du fichier si l'enregistrement a réussi, faux sinon.

        $tmpPath = getUploadedFileTmpPath();
        if ($tmpPath === null) {
            return null;
        }

        $extension = normalizeUploadedExtension($_FILES['file']['name']);
        if ($extension === null) {
            return null;
        }

        $name = generateUUID() . '.' . $extension;

        if (move_uploaded_file($tmpPath, getUploadPath($name))) {
            return $name;
        }

        return null;
    }

function saveImage() : string | null
    {
        // Vérification des données de l'image, puis enregistrement.
        // Retourne Faux si l'image n'en est pas une, ou si elle n'a pas pu être enregistrée.

        $tmpPath = getUploadedFileTmpPath();
        if ($tmpPath === null) {
            return null;
        }

        // Vérifie le type MIME avec finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpPath);
        if (!array_key_exists($mimeType, ALLOWED_IMAGE_MIME_TYPES)) {
            return null;
        }

        $name = generateUUID() . '.' . ALLOWED_IMAGE_MIME_TYPES[$mimeType];

        if (move_uploaded_file($tmpPath, getUploadPath($name))) {
            return $name;
        }

        return null;
    }

function saveMedia() : string | null
    {
        // Vérifie et enregistre une image ou une vidéo.

        $tmpPath = getUploadedFileTmpPath();
        if ($tmpPath === null) {
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpPath);
        if (!array_key_exists($mimeType, ALLOWED_MEDIA_MIME_TYPES)) {
            return null;
        }

        $name = generateUUID() . '.' . ALLOWED_MEDIA_MIME_TYPES[$mimeType];

        if (move_uploaded_file($tmpPath, getUploadPath($name))) {
            return $name;
        }

        return null;
    }

function deleteFile(string $fileName) : bool
    {
        $path = getUploadPath($fileName);

        if (file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }
?>
