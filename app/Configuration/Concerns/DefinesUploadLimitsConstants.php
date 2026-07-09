<?php

namespace App\Configuration\Concerns;

trait DefinesUploadLimitsConstants
{
    public const IMPORT_FILE_SIZE_KB = 25600;

    public const IMPORT_FILE_SIZE_BYTES = self::IMPORT_FILE_SIZE_KB * 1024;

    public const IMPORT_FILE_SIZE_MB = self::IMPORT_FILE_SIZE_KB / 1024;

    public const GENERAL_CONTENT_LENGTH_BYTES = 2 * 1024 * 1024;

    public const IMPORT_CONTENT_LENGTH_BYTES = self::IMPORT_FILE_SIZE_KB * 1024;

    public const DOCUMENT_FILE_SIZE_KB = 5120;

    public const DOCUMENT_FILE_SIZE_BYTES = self::DOCUMENT_FILE_SIZE_KB * 1024;

    public const DOCUMENT_FILE_SIZE_MB = self::DOCUMENT_FILE_SIZE_KB / 1024;

    public const IMAGE_FILE_SIZE_KB = 2048;

    public const IMAGE_FILE_SIZE_BYTES = self::IMAGE_FILE_SIZE_KB * 1024;

    public const IMAGE_FILE_SIZE_MB = self::IMAGE_FILE_SIZE_KB / 1024;

    public const EXCEL_MIME_TYPES = [
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
        ],
        'xls' => [
            'application/vnd.ms-excel',
            'application/msexcel',
        ],
        'csv' => [
            'text/csv',
            'text/plain',
            'application/csv',
        ],
    ];

    public const PDF_MIME_TYPES = [
        'application/pdf',
    ];

    public const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];
}
