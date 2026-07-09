<?php

namespace App\Configuration\Concerns;

trait HandlesUploadLimitsPhpConfigHelpers
{
    public static function getRecommendedPhpConfig(): array
    {
        return [
            'upload_max_filesize' => '8M',
            'post_max_size' => '8M',
            'memory_limit' => '128M',
            'max_execution_time' => '300',
            'max_input_time' => '300',
        ];
    }

    public static function isPhpConfigSafe(): array
    {
        $recommended = self::getRecommendedPhpConfig();
        $current = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
        ];

        $issues = [];

        $uploadMaxBytes = self::convertToBytes($current['upload_max_filesize']);
        $postMaxBytes = self::convertToBytes($current['post_max_size']);
        $memoryLimitBytes = self::convertToBytes($current['memory_limit']);

        $requiredUploadMax = self::convertToBytes($recommended['upload_max_filesize']);

        if ($uploadMaxBytes < $requiredUploadMax) {
            $issues[] = sprintf(
                'upload_max_filesize (%s) es menor que el límite requerido (%s)',
                $current['upload_max_filesize'],
                $recommended['upload_max_filesize']
            );
        }

        $requiredPostMax = max(self::IMPORT_CONTENT_LENGTH_BYTES, self::GENERAL_CONTENT_LENGTH_BYTES);

        if ($postMaxBytes < $requiredPostMax) {
            $issues[] = sprintf(
                'post_max_size (%s) es menor que el límite requerido (%s)',
                $current['post_max_size'],
                self::formatBytes($requiredPostMax, 0)
            );
        }

        if ($memoryLimitBytes < self::convertToBytes($recommended['memory_limit'])) {
            $issues[] = sprintf(
                'memory_limit (%s) es menor que el límite requerido (%s)',
                $current['memory_limit'],
                $recommended['memory_limit']
            );
        }

        return [
            'is_safe' => empty($issues),
            'current' => $current,
            'recommended' => $recommended,
            'issues' => $issues,
        ];
    }

    private static function convertToBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower($value[strlen($value) - 1]);
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
