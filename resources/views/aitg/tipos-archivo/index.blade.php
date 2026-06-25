@extends('adminlte::page')

@section('title', 'Tipos de archivo AITG')

@section('css')
    <x-vite-stylesheet paths="resources/css/aitg/planes-contratacion/app.css" />
@endsection

@section('content_header')
    <h1>Tipos de archivo · Banco de Instructores</h1>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @can('CREAR TIPO ARCHIVO AITG')
        <a href="{{ route('aitg.tipos-archivo.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Nuevo tipo</a>
    @endcan
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead><tr><th>Código</th><th>Nombre</th><th>Obligatorio</th><th>Activo</th><th>Orden</th><th></th></tr></thead>
                <tbody>
                    @foreach($tipos as $tipo)
                        <tr>
                            <td>{{ $tipo->codigo }}</td>
                            <td>{{ $tipo->nombre }}</td>
                            <td>{{ $tipo->es_obligatorio ? 'Sí' : 'No' }}</td>
                            <td>{{ $tipo->activo ? 'Sí' : 'No' }}</td>
                            <td>{{ $tipo->orden }}</td>
                            <td class="text-right">
                                @can('EDITAR TIPO ARCHIVO AITG')
                                    <a href="{{ route('aitg.tipos-archivo.edit', $tipo) }}" class="btn btn-sm btn-info">Editar</a>
                                @endcan
                                @can('ELIMINAR TIPO ARCHIVO AITG')
                                    <form action="{{ route('aitg.tipos-archivo.destroy', $tipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
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
        <div class="card-footer">{{ $tipos->links() }}</div>
    </div>
</div>
@endsection
