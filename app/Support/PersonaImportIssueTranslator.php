<?php

namespace App\Support;

use Illuminate\Support\Str;

final class PersonaImportIssueTranslator
{
    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'missing_document' => 'Documento de identidad ausente',
            'missing_document_type' => 'Tipo de documento no válido o no encontrado',
            'missing_required_fields' => 'Faltan nombres o apellidos obligatorios',
            'duplicate_document_in_file' => 'Documento repetido en el archivo',
            'duplicate_document_existing' => 'Documento ya registrado en el sistema',
            'duplicate_email_in_file' => 'Correo repetido en el archivo',
            'duplicate_email_existing' => 'Correo electrónico ya registrado en el sistema',
            'duplicate_celular_in_file' => 'Celular repetido en el archivo',
            'duplicate_celular_existing' => 'Número de celular ya registrado en el sistema',
            'duplicate_telefono_in_file' => 'Teléfono repetido en el archivo',
            'duplicate_telefono_existing' => 'Número de teléfono ya registrado en el sistema',
            'duplicate_generic' => 'Datos duplicados en el sistema',
            'partial_import_email' => 'Importado sin email (ya existe en el sistema)',
            'partial_import_celular' => 'Importado sin celular (ya existe en el sistema)',
            'partial_import_telefono' => 'Importado sin teléfono (ya existe en el sistema)',
            'partial_import_email_celular' => 'Importado sin email y celular (ya existen en el sistema)',
            'partial_import_email_telefono' => 'Importado sin email y teléfono (ya existen en el sistema)',
            'partial_import_celular_telefono' => 'Importado sin celular y teléfono (ya existen en el sistema)',
            'partial_import_email_celular_telefono' => 'Importado sin contactos (email, celular y teléfono ya existen)',
        ];
    }

    public static function traducir(?string $issueType, ?string $errorMessage = null): string
    {
        $labels = self::labels();

        if ($issueType === null) {
            return 'Incidencia sin clasificar';
        }

        if ($issueType === 'persist_error' && $errorMessage) {
            $detectedType = self::detectarTipoErrorDesdeMensaje($errorMessage);
            if ($detectedType !== null && isset($labels[$detectedType])) {
                return $labels[$detectedType];
            }

            return 'Error al guardar el registro. Verifique que los datos no estén duplicados.';
        }

        return $labels[$issueType] ?? Str::headline(str_replace('_', ' ', $issueType));
    }

    public static function detectarTipoErrorDesdeMensaje(string $errorMessage): ?string
    {
        if (str_contains($errorMessage, 'personas_email_unique')
            || (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, '@'))) {
            return 'duplicate_email_existing';
        }

        if (str_contains($errorMessage, 'personas_numero_documento_unique')
            || (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, 'numero_documento'))) {
            return 'duplicate_document_existing';
        }

        if (str_contains($errorMessage, 'personas_celular_unique')
            || (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, 'celular'))) {
            return 'duplicate_celular_existing';
        }

        if (str_contains($errorMessage, 'personas_telefono_unique')
            || (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, 'telefono'))) {
            return 'duplicate_telefono_existing';
        }

        if (str_contains($errorMessage, 'Duplicate entry')) {
            return 'duplicate_generic';
        }

        return null;
    }
}
