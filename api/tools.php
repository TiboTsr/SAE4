<?php

require_once 'DB.php';

class tools
{
    private const ALLOWED_UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'xls', 'xlsx'];
    private const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private static function normalizeUploadedExtension(string $fileName): ?string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($extension, self::ALLOWED_UPLOAD_EXTENSIONS, true)) {
            return null;
        }

        return $extension;
    }

    private static function getUploadedFileTmpPath(): ?string
    {
        if (!isset($_FILES['file']) || $_FILES['file']['tmp_name'] === '') {
            return null;
        }

        return $_FILES['file']['tmp_name'];
    }

    private static function getUploadPath(string $fileName): string
    {
        return 'files/' . $fileName;
    }

    public static function generateUUID()
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return bin2hex($data);
    }

    public static function saveFile()
    {
        // Retourne le nom du fichier si l'enregistrement a réussi, faux sinon.

        $tmpPath = self::getUploadedFileTmpPath();
        if ($tmpPath === null) {
            return false;
        }

        $extension = self::normalizeUploadedExtension($_FILES['file']['name']);
        if ($extension === null) {
            return false;
        }

        $name = self::generateUUID() . '.' . $extension;

        if (move_uploaded_file($tmpPath, self::getUploadPath($name))) {
            return DB::clean($name);
        }

        return false;
    }

    public static function saveImage()
    {
        // Vérification des données de l'image, puis enregistrement.
        // Retourne Faux si l'image n'en est pas une, ou si elle n'a pas pu être enregistrée.

        $tmpPath = self::getUploadedFileTmpPath();
        if ($tmpPath === null) {
            return false;
        }

        // Vérifie le type MIME avec finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpPath);
        if (!array_key_exists($mimeType, self::ALLOWED_IMAGE_MIME_TYPES)) {
            return false;
        }

        $name = self::generateUUID() . '.' . self::ALLOWED_IMAGE_MIME_TYPES[$mimeType];

        if (move_uploaded_file($tmpPath, self::getUploadPath($name))) {
            return DB::clean($name);
        }

        return false;
    }


    public static function deleteFile($fileName)
    {
        $path = self::getUploadPath($fileName);

        if (file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }


    public static function methodAccepted(...$acceptedContentType)
    {
        // On récupère le Content-Type de la requête (chaine vide si non précisé)
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // Si le Content-Type de la requête est dans la liste des types acceptés, on retourne vrai
        foreach ($acceptedContentType as $type) {
            if (str_starts_with($contentType, $type)) {
                return true;
            }
        }

        // Erreur si le Content-Type n'est pas supporté
        http_response_code(415);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Unsupported Media Type',
            'message' => "Content-Type '{$contentType}' is not supported. Accepted types: " . implode(', ', $acceptedContentType)
        ]);

        // On arrête le script
        exit;
    }

    public static function hasPermission($permission): bool
    {

        if (!isset($_SESSION['userid'])) {
            return false;
        }

        $db = new \DB();
        $perms = $db->select("SELECT * FROM LISTE_PERMISSIONS WHERE id_membre = ?", 'i', [$_SESSION['userid']]);

        if (count($perms) == 0 || !isset($perms[0][$permission]) || $perms[0][$permission] == 0) {
            return false;
        }

        return true;
    }

    public static function checkPermission($permission): void
    {
        if (self::hasPermission($permission) === false) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.'
            ]);
            exit;
        }
    }
}
