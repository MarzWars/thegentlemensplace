<?php
// app/Services/FileUpload.php
namespace App\Services;

class FileUpload
{
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE      = 10 * 1024 * 1024; // 10MB
    private const UPLOAD_DIR    = PUBLIC_PATH . '/uploads/performers/';

    public static function savePerformerPhoto(array $file, int $performerId): array
    {
        // 1. Validate file existence
        if ($file['error'] !== UPLOAD_ERR_OK) throw new \Exception('Upload error: ' . $file['error']);

        // 2. Check size
        if ($file['size'] > self::MAX_SIZE) throw new \Exception('File too large (max 10MB).');

        // 3. Check MIME type via fileinfo (NOT $_FILES['type'] — that's user-controlled)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_TYPES)) throw new \Exception('Invalid file type.');

        // 4. Generate safe, random filename — NEVER use original filename
        $ext      = match($mime) { 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp' };
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        // 5. Create per-performer directory
        $dir = self::UPLOAD_DIR . $performerId . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $destPath = $dir . $filename;

        // 6. Move to destination
        if (!move_uploaded_file($file['tmp_name'], $destPath)) throw new \Exception('Failed to save file.');

        // 7. Create thumbnail (requires GD)
        if (extension_loaded('gd')) {
            try {
                $thumbPath = self::createThumbnail($destPath, $dir . 'thumb_' . $filename);
            } catch (\Exception $e) {
                copy($destPath, $dir . 'thumb_' . $filename);
            }
        } else {
            // Fallback if GD extension is not loaded in PHP
            copy($destPath, $dir . 'thumb_' . $filename);
        }

        return [
            'path'  => 'uploads/performers/' . $performerId . '/' . $filename,
            'thumb' => 'uploads/performers/' . $performerId . '/thumb_' . $filename,
        ];
    }

    private static function createThumbnail(string $src, string $dest, int $width = 400, int $height = 500): string
    {
        [$origW, $origH, $type] = getimagesize($src);
        $img = match($type) {
            IMAGETYPE_JPEG => \imagecreatefromjpeg($src),
            IMAGETYPE_PNG  => \imagecreatefrompng($src),
            IMAGETYPE_WEBP => \imagecreatefromwebp($src),
            default        => throw new \Exception('Unsupported image type'),
        };

        $thumb = \imagecreatetruecolor($width, $height);
        \imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $origW, $origH);
        \imagejpeg($thumb, $dest, 85);
        \imagedestroy($img);
        \imagedestroy($thumb);

        return $dest;
    }

    public static function savePerformerVideo(array $file, int $performerId): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) throw new \Exception('Upload error: ' . $file['error']);
        if ($file['size'] > 20 * 1024 * 1024) throw new \Exception('Video too large (max 20MB).');

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if ($mime !== 'video/mp4') throw new \Exception('Invalid video format. Only MP4 videos are allowed.');

        $filename = 'video_' . bin2hex(random_bytes(8)) . '.mp4';
        $dir = self::UPLOAD_DIR . $performerId . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $destPath = $dir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) throw new \Exception('Failed to save video.');

        return 'uploads/performers/' . $performerId . '/' . $filename;
    }

    public static function savePerformerVoice(array $file, int $performerId): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) throw new \Exception('Upload error: ' . $file['error']);
        if ($file['size'] > 10 * 1024 * 1024) throw new \Exception('Audio too large (max 10MB).');

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        
        $allowedMimes = ['audio/mpeg', 'audio/mp3', 'audio/mp4', 'video/mp4'];
        if (!in_array($mime, $allowedMimes)) {
            throw new \Exception('Invalid voice format. Only MP3 and MP4 formats are allowed.');
        }

        $ext = ($mime === 'audio/mpeg' || $mime === 'audio/mp3') ? 'mp3' : 'mp4';
        $filename = 'voice_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dir = self::UPLOAD_DIR . $performerId . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $destPath = $dir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) throw new \Exception('Failed to save voice sample.');

        return 'uploads/performers/' . $performerId . '/' . $filename;
    }
}