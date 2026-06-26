@extends('adminlte::page')

@section('title', 'Candidatos - Selección AITG')

@section('css')
    <x-vite-stylesheet paths="resources/css/aitg/planes-contratacion/app.css" />
@endsection

@section('content_header')
    @include('aitg.planes-contratacion.partials.layout.page-header', [
        'title' => 'Selección — ' . ($convocatoria->titulo ?? ''),
        'subtitle' => ($convocatoria->competencia->nombre ?? '') . ' · ' . ($convocatoria->regional->nombre ?? ''),
        'breadcrumb' => [
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Selección', 'url' => route('aitg.seleccion.index')],
            ['label' => 'Candidatos', 'active' => true],
        ],
    ])
@endsection

@section('content')
<section class="content aitg-content mt-2">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($convocatoria->estado === 'finalizada' && $convocatoria->postulacionSeleccionada)
            <div class="alert alert-info">
                <strong>Instructor seleccionado:</strong>
                {{ trim(($convocatoria->postulacionSeleccionada->user->persona->primer_nombre ?? '') . ' ' . ($convocatoria->postulacionSeleccionada->user->persona->primer_apellido ?? '')) }}
                ({{ $convocatoria->postulacionSeleccionada->user->persona->numero_documento ?? '—' }})
            </div>
        @endif

        <div class="mb-3 d-flex flex-wrap align-items-center">
            <a href="{{ route('aitg.seleccion.index') }}" class="btn btn-outline-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <form method="GET" class="form-inline">
                <label class="mr-2">Ordenar por puntaje:</label>
                <select name="orden" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="desc" @selected($orden === 'desc')>Mayor a menor</option>
                    <option value="asc" @selected($orden === 'asc')>Menor a mayor</option>
                </select>
            </form>
        </div>

        <div class="aitg-card aitg-card--primary mb-3">
            <div class="aitg-card__body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Perfil</th>
                                <th>% Checklist</th>
                                <th>Bonus</th>
                                <th>Total ranking</th>
                                <th>Empate</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidatos as $index => $postulacion)
                                @php
                                    $persona = $postulacion->user->persona;
                                    $eval = $postulacion->evaluacion;
                                @endphp
                                <tr @if($postulacion->en_empate) class="table-warning" @endif>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $persona->numero_documento ?? '—' }}</td>
                                    <td>{{ trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) ?: $postulacion->user->email }}</td>
                                    <td><small>{{ $postulacion->perfilPlan->descripcion_criterio ?? '—' }}</small></td>
                                    <td>{{ number_format($eval->puntaje_checklist ?? 0, 2) }}</td>
                                    <td>{{ number_format($eval->puntaje_adicionales ?? 0, 2) }}</td>
                                    <td><strong>{{ number_format($eval->puntaje_total ?? 0, 2) }}</strong></td>
                                    <td>
                                        @if($postulacion->en_empate)
                                            <span class="badge badge-warning">Empate</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($postulacion->evaluacion)
                                            <a href="{{ route('aitg.evaluacion.show', $postulacion->evaluacion) }}" class="btn btn-xs btn-outline-primary">Ver evaluación</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted">No hay candidatos con evaluación aprobada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($convocatoria->estado !== 'finalizada' && $candidatos->isNotEmpty())
            <div class="aitg-card aitg-card--primary">
                <div class="aitg-card__header"><h3 class="h6 mb-0">Confirmar selección</h3></div>
                <div class="aitg-card__body">
                    <form method="POST" action="{{ route('aitg.seleccion.confirmar', $convocatoria) }}" onsubmit="return confirm('¿Confirmar la selección del instructor ganador? La convocatoria quedará finalizada.');">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Instructor ganador <span class="text-danger">*</span></label>
                                    <select name="postulacion_ganador_id" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($candidatos as $postulacion)
                                            <option value="{{ $postulacion->id }}">
                                                {{ $postulacion->user->persona->numero_documento ?? $postulacion->id }} —
                                                {{ trim(($postulacion->user->persona->primer_nombre ?? '') . ' ' . ($postulacion->user->persona->primer_apellido ?? '')) }}
                                                ({{ number_format($postulacion->evaluacion->puntaje_total ?? 0, 2) }} pts)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Suplente (opcional)</label>
                                    <select name="postulacion_suplente_id" class="form-control">
                                        <option value="">Ninguno</option>
                                        @foreach($candidatos as $postulacion)
                                            <option value="{{ $postulacion->id }}">
                                                {{ $postulacion->user->persona->numero_documento ?? $postulacion->id }} —
                                                {{ trim(($postulacion->user->persona->primer_nombre ?? '') . ' ' . ($postulacion->user->persona->primer_apellido ?? '')) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones finales</label>
                                    <textarea name="observaciones" rows="3" class="form-control" placeholder="Observaciones del comité de selección..."></textarea>
                                </div>
                            </div>
                        </div>
                        @can('SELECCIONAR INSTRUCTOR AITG')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-check"></i> Confirmar selección
                            </button>
                        @endcan
                    </form>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
