@extends('adminlte::page')

@section('title', 'Crear Aspirante - ' . $programa->nombre)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mb-0">
                <i class="fas fa-user-plus me-2"></i>Crear Nuevo Aspirante
            </h1>
            <p class="text-muted mb-0">Programa: {{ $programa->nombre }}</p>
        </div>
        <a href="{{ route('aspirantes.programa', ['programa' => $programa->id]) }}"
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>¡Atención!</strong> Por favor, corrige los siguientes errores:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (request('numero_documento'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Persona no encontrada:</strong> Complete el formulario para crear un nuevo aspirante con el documento: <strong>{{ request('numero_documento') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title m-0 font-weight-bold text-primary">
                <i class="fas fa-user-plus mr-2"></i>Información del Aspirante
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('aspirantes.store-new', ['programa' => $programa->id]) }}"
                  autocomplete="off" enctype="multipart/form-data">
                @csrf

                @php
                    // Pre-llenar número de documento si viene en la URL
                    $numeroDocumentoPrellenado = old('numero_documento', request('numero_documento'));
                @endphp

                @include('personas.partials.form', [
                    'persona' => null,
                    'documentos' => $documentos,
                    'generos' => $generos,
                    'caracterizaciones' => $caracterizaciones,
                    'paises' => $paises,
                    'departamentos' => $departamentos,
                    'municipios' => $municipios,
                    'vias' => $vias,
                    'letras' => $letras,
                    'cardinales' => $cardinales,
                    'showCaracterizacion' => true,
                    'numeroDocumentoPrellenado' => $numeroDocumentoPrellenado,
                ])

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title m-0 text-primary">
                            <i class="fas fa-id-card mr-2"></i>Documento de identidad
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            Por favor cargue una copia legible en PDF del documento de identidad. El archivo no debe superar los 5MB.
                        </div>
                        <div class="form-group">
                            <label for="documento_identidad" class="form-label font-weight-bold">
                                Documento de identidad (PDF) <span class="text-danger">*</span>
                            </label>
                            <input type="file"
                                   class="form-control @error('documento_identidad') is-invalid @enderror"
                                   id="documento_identidad" name="documento_identidad" accept=".pdf" required>
                            <small class="form-text text-muted">
                                Solo se permiten archivos PDF. Tamaño máximo: 5MB.
                            </small>
                            @error('documento_identidad')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title m-0 text-primary">
                            <i class="fas fa-comment-alt mr-2"></i> Observaciones
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="form-group">
                            <label for="observaciones">Observaciones (opcional)</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                placeholder="Ingrese observaciones adicionales sobre el aspirante...">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <hr class="mt-4">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('aspirantes.programa', ['programa' => $programa->id]) }}"
                           class="btn btn-outline-secondary btn-sm mx-1">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-outline-success btn-sm mx-1">
                            <i class="fas fa-save mr-1"></i> Crear Aspirante
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop

