@extends('adminlte::page')

@section('title', 'Editar tipo de archivo AITG')

@section('content_header')<h1>Editar tipo de archivo</h1>@endsection

@section('content')
<div class="container-fluid">
    @include('aitg.tipos-archivo.partials.form', [
        'action' => route('aitg.tipos-archivo.update', $tipo),
        'method' => 'PUT',
        'tipo' => $tipo,
    ])
</div>
@endsection
