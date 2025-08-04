<?php
// includes/Uploads.php

class Uploads {
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;

    public function __construct() {
        $this->uploadDir = UPLOAD_DIR;
        $this->allowedTypes = ALLOWED_TYPES;
        $this->maxSize = MAX_FILE_SIZE;

        // Crear directorio si no existe
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadFile($file, $prefix = 'doc') {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error en la subida del archivo");
        }

        // Validar tipo
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new Exception("Tipo de archivo no permitido");
        }

        // Validar tamaño
        if ($file['size'] > $this->maxSize) {
            throw new Exception("El archivo excede el tamaño máximo permitido");
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . uniqid() . '.' . $extension;
        $filepath = $this->uploadDir . $filename;

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Error al guardar el archivo");
        }

        return $filepath;
    }

    public function deleteFile($filepath) {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    public function getFileUrl($filepath) {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $filepath);
    }
}
?>