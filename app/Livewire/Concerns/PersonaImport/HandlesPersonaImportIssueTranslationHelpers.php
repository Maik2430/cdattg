<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Support\PersonaImportIssueTranslator;

trait HandlesPersonaImportIssueTranslationHelpers
{
    private function traducirIssueType(?string $issueType, ?string $errorMessage = null): string
    {
        return PersonaImportIssueTranslator::traducir($issueType, $errorMessage);
    }
}
