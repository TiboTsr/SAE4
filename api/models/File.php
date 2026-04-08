<?php

namespace model;

require_once __DIR__ . '/BaseModel.php';

use finfo;
use JsonSerializable;
use tools;

class File implements JsonSerializable
{
    private string $fileName;

    private const UPLOAD_DIRECTORY = 'files';
    private const ALLOWED_UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'xls', 'xlsx'];
    private const ALLOWED_PUT_PATCH_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-excel' => 'xls',
    ];
    private const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    private static function getUploadDirectoryPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . self::UPLOAD_DIRECTORY;
    }

    private static function ensureUploadDirectory(): bool
    {
        $directory = self::getUploadDirectoryPath();

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0775, true) && !is_dir($directory)) {
                error_log("Upload directory cannot be created: " . $directory);
                return false;
            }
            @chmod($directory, 0775);
        }

        if (!is_writable($directory)) {
            error_log("Upload directory is not writable: " . $directory);
            return false;
        }

        return true;
    }

    private static function getUploadPath(string $fileName): string
    {
        return self::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . $fileName;
    }

    private static function detectMimeType(string $path): ?string
    {
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($path);
            if (is_string($mimeType) && $mimeType !== '') {
                return $mimeType;
            }
        }

        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($path);
            if (is_string($mimeType) && $mimeType !== '') {
                return $mimeType;
            }
        }

        return null;
    }

    private static function getPhpUploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE form value',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
            default => 'Unknown upload error',
        };
    }

    private static function normalizeUploadedExtension(string $fileName): ?string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($extension, self::ALLOWED_UPLOAD_EXTENSIONS, true)) {
            return null;
        }

        return $extension;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }


    private function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public static function getFile(string | null $fileName): File | null
    {
        if (!is_null($fileName) && file_exists(self::getUploadPath($fileName))) {
            return new File($fileName);
        }

        return null;
    }


    // Non, je ne souhaite pas expliquer ce code.
    // Il a été (honnetement) généré via Claude (ia) car PHP refuse de
    // mettre les fichiers dans $_FILES si la requête n'est pas un POST.
    // Or, on utilise PUT et PATCH pour les fichiers.
    // Au moment d'écrire ces lignes, je suis vraiment enervé contre PHP.
    // Villain php
    public static function saveFile(): File | null
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Gestion des requêtes POST (formulaires classiques)
        if ($method === 'POST' && isset($_FILES['file'])) {
            if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                error_log('File upload failed: ' . self::getPhpUploadErrorMessage((int)$_FILES['file']['error']));
                return null;
            }

            $extension = self::normalizeUploadedExtension($_FILES['file']['name']);
            if ($extension === null) {
                return null;
            }

            if (!self::ensureUploadDirectory()) {
                return null;
            }

            $name = tools::generateUUID() . '.' . $extension;
            $uploadPath = self::getUploadPath($name);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                chmod($uploadPath, 0644);
                return new File($name);
            }
            return null;
        }

        // Gestion des requêtes PUT/PATCH et POST brut (fallback legacy)
        if ($method === 'PUT' || $method === 'PATCH' || $method === 'POST') {
            // Lecture du corps de la requête
            $putData = fopen('php://input', 'r');

            // Création d'un fichier temporaire
            $tempFile = tempnam(sys_get_temp_dir(), 'upload_');

            // S'assurer que le fichier temporaire est créé avec les bonnes permissions
            chmod($tempFile, 0644);

            $tempHandle = fopen($tempFile, 'w');

            // Copie des données
            stream_copy_to_stream($putData, $tempHandle);

            // Fermeture des flux
            fclose($putData);
            fclose($tempHandle);

            // Détection du type de fichier
            $mimeType = self::detectMimeType($tempFile);

            // Détermination de l'extension basée sur le type MIME
            $extension = $mimeType !== null ? (self::ALLOWED_PUT_PATCH_MIME_TYPES[$mimeType] ?? null) : null;
            if ($extension === null) {
                @unlink($tempFile);
                return null;
            }

            if (!self::ensureUploadDirectory()) {
                @unlink($tempFile);
                return null;
            }

            $name = tools::generateUUID() . '.' . $extension;
            $uploadPath = self::getUploadPath($name);

            // Déplacement du fichier vers sa destination finale
            if (rename($tempFile, $uploadPath)) {
                return new File($name);
            }

            // Nettoyage en cas d'échec
            @unlink($tempFile);
            return null;
        }

        return null;
    }

    // cf. mon commentaire de la méthode ci dessus
    public static function saveImage(): File | null
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST' && isset($_FILES['file'])) {
            if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                error_log('Image upload failed: ' . self::getPhpUploadErrorMessage((int)$_FILES['file']['error']));
                return null;
            }

            $mimeType = self::detectMimeType($_FILES['file']['tmp_name']);
            $extension = $mimeType !== null ? (self::ALLOWED_IMAGE_MIME_TYPES[$mimeType] ?? null) : null;
            if ($extension === null) {
                return null;
            }

            if (!self::ensureUploadDirectory()) {
                return null;
            }

            $name = tools::generateUUID() . '.' . $extension;
            $uploadPath = self::getUploadPath($name);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                chmod($uploadPath, 0644);
                return new File($name);
            }

            return null;
        }

        if ($method !== 'PUT' && $method !== 'PATCH' && $method !== 'POST') {
            return null;
        }

        // Lecture du corps brut de la requête
        $rawData = file_get_contents('php://input');
        if (!$rawData) {
            return null; // Pas de données brutes
        }

        // Création d'un fichier temporaire pour analyser l'image
        $tmpFile = tempnam(sys_get_temp_dir(), 'upload_');
        file_put_contents($tmpFile, $rawData);

        // Vérification du type MIME
        $mimeType = self::detectMimeType($tmpFile);
        $extension = $mimeType !== null ? (self::ALLOWED_IMAGE_MIME_TYPES[$mimeType] ?? null) : null;
        if ($extension === null) {
            unlink($tmpFile); // Nettoyage
            return null; // Type non autorisé
        }

        if (!self::ensureUploadDirectory()) {
            unlink($tmpFile);
            return null;
        }

        $name = tools::generateUUID() . '.' . $extension;
        $uploadPath = self::getUploadPath($name);

        if (rename($tmpFile, $uploadPath)) {
            chmod($uploadPath, 0644);
            return new File($name);
        }

        // Nettoyage du fichier temporaire après échec
        unlink($tmpFile);
        return null;
    }


    public function deleteFile(): bool
    {
            $path = self::getUploadPath($this->fileName);

        if (file_exists($path)) {
            unlink($path);
            return true;
        }

            return false;
    }

    public function __toString(): string
    {
        return $this->fileName;
    }


    public function jsonSerialize(): string
    {
        return $this->fileName;
    }
}
