@extends('adminlte::page')

@section('title', 'Motivos de rechazo AITG')

@section('content_header')<h1>Motivos de rechazo · Banco de Instructores</h1>@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @can('CREAR MOTIVO RECHAZO AITG')
        <a href="{{ route('aitg.motivos-rechazo.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Nuevo motivo</a>
    @endcan
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead><tr><th>Código</th><th>Nombre</th><th>Activo</th><th>Orden</th><th></th></tr></thead>
                <tbody>
                    @foreach($motivos as $motivo)
                        <tr>
                            <td>{{ $motivo->codigo }}</td>
                            <td>{{ $motivo->nombre }}</td>
                            <td>{{ $motivo->activo ? 'Sí' : 'No' }}</td>
                            <td>{{ $motivo->orden }}</td>
                            <td class="text-right">
                                @can('EDITAR MOTIVO RECHAZO AITG')
                                    <a href="{{ route('aitg.motivos-rechazo.edit', $motivo) }}" class="btn btn-sm btn-info">Editar</a>
                                @endcan
                                @can('ELIMINAR MOTIVO RECHAZO AITG')
                                    <form action="{{ route('aitg.motivos-rechazo.destroy', $motivo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $motivos->links() }}</div>
    </div>
</div>
@endsection
