<?php

namespace model;

require_once __DIR__ . '/BaseModel.php';

use finfo;
use JsonSerializable;
use tools;

class File implements JsonSerializable
{
    private string $fileName;

    private const UPLOAD_DIRECTORY = 'files/';
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

    private static function getUploadPath(string $fileName): string
    {
        return self::UPLOAD_DIRECTORY . $fileName;
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
        if ($method === 'POST') {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                return null;
            }

            $extension = self::normalizeUploadedExtension($_FILES['file']['name']);
            if ($extension === null) {
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

        // Gestion des requêtes PUT/PATCH
        if ($method === 'PUT' || $method === 'PATCH') {
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
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($tempFile);

            // Détermination de l'extension basée sur le type MIME
            $extension = self::ALLOWED_PUT_PATCH_MIME_TYPES[$mimeType] ?? null;
            if ($extension === null) {
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

        if ($method === 'POST') {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                return null;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['file']['tmp_name']);
            $extension = self::ALLOWED_IMAGE_MIME_TYPES[$mimeType] ?? null;
            if ($extension === null) {
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

        if ($method !== 'PUT' && $method !== 'PATCH') {
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
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpFile);
        $extension = self::ALLOWED_IMAGE_MIME_TYPES[$mimeType] ?? null;
        if ($extension === null) {
            unlink($tmpFile); // Nettoyage
            return null; // Type non autorisé
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
