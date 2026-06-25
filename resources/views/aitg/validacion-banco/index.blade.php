@extends('adminlte::page')

@section('title', 'Validación Banco de Instructores - AITG')

@section('css')
    <x-vite-stylesheet paths="resources/css/aitg/planes-contratacion/app.css" />
@endsection

@section('content_header')
    @include('aitg.planes-contratacion.partials.layout.page-header', [
        'title' => 'Validación de solicitudes',
        'subtitle' => 'Banco de Instructores · Revisión de documentos',
        'breadcrumb' => [
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'AITG', 'icon' => 'fa-users-cog'],
            ['label' => 'Validación', 'active' => true],
        ],
    ])
@endsection

@section('content')
<section class="content aitg-content mt-2">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="aitg-card aitg-card--primary">
            <div class="aitg-card__body">
                <form method="GET" class="row mb-3">
                    <div class="col-md-4">
                        <select name="estado" class="form-control">
                            <option value="">Todos los estados</option>
                            @foreach(\App\Models\Aitg\Banco\SolicitudBanco::ESTADOS as $val => $label)
                                @if($val !== 'borrador')
                                    <option value="{{ $val }}" @selected(request('estado') === $val)>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><button class="btn btn-secondary btn-block">Filtrar</button></div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Aspirante</th>
                                <th>Documento</th>
                                <th>Estado</th>
                                <th>Enviada</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->id }}</td>
                                    <td>{{ trim(($solicitud->user->persona->primer_nombre ?? '') . ' ' . ($solicitud->user->persona->primer_apellido ?? '')) ?: $solicitud->user->email }}</td>
                                    <td>{{ $solicitud->user->persona?->numero_documento ?? '—' }}</td>
                                    <td><span class="badge badge-info">{{ $solicitud->estado_label }}</span></td>
                                    <td>{{ $solicitud->fecha_envio?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('aitg.validacion-banco.show', $solicitud) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-search"></i> Revisar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">No hay solicitudes pendientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $solicitudes->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
