<?php

class ImageUploader {
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxFileSize = 8 * 1024 * 1024;

    public function __construct($uploadDir = 'uploads/') {
        $this->uploadDir = $uploadDir;

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function upload($fileInputName): string {
        if (!isset($_FILES[$fileInputName])) {
            throw new Exception("Файл не был загружен.");
        }

        $file = $_FILES[$fileInputName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Ошибка при загрузке файла.");
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new Exception("Файл слишком большой.");
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $destination = $this->uploadDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Не удалось сохранить файл.");
        }

        return $destination;
    }
}