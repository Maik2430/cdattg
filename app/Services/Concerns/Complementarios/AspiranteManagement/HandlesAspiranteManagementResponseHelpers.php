<?php

namespace App\Services\Concerns\Complementarios\AspiranteManagement;

trait HandlesAspiranteManagementResponseHelpers
{
    private function createErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
        ];
    }

    private function createSuccessResponse(string $message): array
    {
        return [
            'success' => true,
            'message' => $message,
        ];
    }
}
