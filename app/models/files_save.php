<?php
const UPLOAD_DIRECTORY = 'api/files/';
const ALLOWED_UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'xls', 'xlsx'];
const ALLOWED_IMAGE_MIME_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
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