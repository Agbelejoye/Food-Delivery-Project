<?php
require_once __DIR__ . '/../core/Controller.php';

class UploadController extends Controller {

    public function store($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        try {
            if (!isset($_FILES['file'])) {
                return $this->sendError('No file uploaded', 400);
            }

            $file = $_FILES['file'];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return $this->sendError('File upload error: ' . $this->getUploadErrorMessage($file['error']), 400);
            }

            // Validate file size
            if ($file['size'] > MAX_FILE_SIZE) {
                return $this->sendError('File size exceeds maximum allowed size', 400);
            }

            // Validate file type
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
                return $this->sendError('File type not allowed', 400);
            }

            // Validate MIME type
            $allowedMimeTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'
            ];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimeTypes)) {
                return $this->sendError('Invalid file type', 400);
            }

            // Create upload directory if it doesn't exist
            if (!is_dir(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0755, true);
            }

            // Generate unique filename
            $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
            $filePath = UPLOAD_PATH . $fileName;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                return $this->sendError('Failed to save file', 500);
            }

            // Create thumbnail for images
            $thumbnailPath = $this->createThumbnail($filePath, $fileName);

            // Save file info to database (optional)
            $stmt = $this->db->prepare("
                INSERT INTO uploaded_files (user_id, original_name, file_name, file_path, file_size, mime_type, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            // Create table if it doesn't exist
            $this->createUploadedFilesTable();
            
            $stmt->execute([
                $this->user['id'],
                $file['name'],
                $fileName,
                $filePath,
                $file['size'],
                $mimeType
            ]);

            $fileUrl = BACKEND_URL . '/uploads/' . $fileName;
            $thumbnailUrl = $thumbnailPath ? BACKEND_URL . '/uploads/thumbnails/' . basename($thumbnailPath) : null;

            return $this->sendResponse([
                'file_name' => $fileName,
                'original_name' => $file['name'],
                'file_url' => $fileUrl,
                'thumbnail_url' => $thumbnailUrl,
                'file_size' => $file['size'],
                'mime_type' => $mimeType
            ], 201, 'File uploaded successfully');

        } catch (Exception $e) {
            return $this->sendError('File upload failed: ' . $e->getMessage(), 500);
        }
    }

    private function createThumbnail($originalPath, $fileName) {
        try {
            $thumbnailDir = UPLOAD_PATH . 'thumbnails/';
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $thumbnailPath = $thumbnailDir . 'thumb_' . $fileName;
            $maxWidth = 300;
            $maxHeight = 300;

            // Get image info
            $imageInfo = getimagesize($originalPath);
            if (!$imageInfo) {
                return null;
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $imageType = $imageInfo[2];

            // Calculate new dimensions
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);

            // Create image resource based on type
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($originalPath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($originalPath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($originalPath);
                    break;
                case IMAGETYPE_WEBP:
                    $sourceImage = imagecreatefromwebp($originalPath);
                    break;
                default:
                    return null;
            }

            if (!$sourceImage) {
                return null;
            }

            // Create thumbnail
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG and GIF
            if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Save thumbnail
            $success = false;
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $success = imagejpeg($thumbnail, $thumbnailPath, 85);
                    break;
                case IMAGETYPE_PNG:
                    $success = imagepng($thumbnail, $thumbnailPath, 8);
                    break;
                case IMAGETYPE_GIF:
                    $success = imagegif($thumbnail, $thumbnailPath);
                    break;
                case IMAGETYPE_WEBP:
                    $success = imagewebp($thumbnail, $thumbnailPath, 85);
                    break;
            }

            imagedestroy($sourceImage);
            imagedestroy($thumbnail);

            return $success ? $thumbnailPath : null;

        } catch (Exception $e) {
            error_log("Thumbnail creation failed: " . $e->getMessage());
            return null;
        }
    }

    private function createUploadedFilesTable() {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS uploaded_files (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    original_name VARCHAR(255) NOT NULL,
                    file_name VARCHAR(255) NOT NULL,
                    file_path VARCHAR(500) NOT NULL,
                    file_size INT NOT NULL,
                    mime_type VARCHAR(100) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ";
            $this->db->exec($sql);
        } catch (Exception $e) {
            error_log("Failed to create uploaded_files table: " . $e->getMessage());
        }
    }

    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
}
?>
