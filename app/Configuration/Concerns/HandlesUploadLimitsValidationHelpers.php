<?php

namespace App\Configuration\Concerns;

trait HandlesUploadLimitsValidationHelpers
{
    public static function formatBytes(int $bytes, int $decimals = 2): string
    {
        if ($bytes === 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), $decimals).' '.$sizes[$i];
    }

    public static function isWithinLimit(int $sizeInBytes, int $limitInBytes): bool
    {
        return $sizeInBytes > 0 && $sizeInBytes <= $limitInBytes;
    }

    public static function getImportLimit(string $format = 'MB'): int|float
    {
        return match (strtoupper($format)) {
            'KB' => self::IMPORT_FILE_SIZE_KB,
            'BYTES' => self::IMPORT_FILE_SIZE_BYTES,
            'MB' => self::IMPORT_FILE_SIZE_MB,
            default => self::IMPORT_FILE_SIZE_BYTES,
        };
    }

    public static function getDocumentLimit(string $format = 'MB'): int|float
    {
        return match (strtoupper($format)) {
            'KB' => self::DOCUMENT_FILE_SIZE_KB,
            'BYTES' => self::DOCUMENT_FILE_SIZE_BYTES,
            'MB' => self::DOCUMENT_FILE_SIZE_MB,
            default => self::DOCUMENT_FILE_SIZE_BYTES,
        };
    }

    public static function getImageLimit(string $format = 'MB'): int|float
    {
        return match (strtoupper($format)) {
            'KB' => self::IMAGE_FILE_SIZE_KB,
            'BYTES' => self::IMAGE_FILE_SIZE_BYTES,
            'MB' => self::IMAGE_FILE_SIZE_MB,
            default => self::IMAGE_FILE_SIZE_BYTES,
        };
    }

    public static function isValidExcelMimeType(string $extension, string $mimeType): bool
    {
        $extension = strtolower($extension);

        if (! isset(self::EXCEL_MIME_TYPES[$extension])) {
            return false;
        }

        return in_array($mimeType, self::EXCEL_MIME_TYPES[$extension], true);
    }
}
