@extends('adminlte::page')

@section('title', 'Crear tipo de archivo AITG')

@section('content_header')<h1>Nuevo tipo de archivo</h1>@endsection

@section('content')
<div class="container-fluid">
    @include('aitg.tipos-archivo.partials.form', [
        'action' => route('aitg.tipos-archivo.store'),
        'method' => 'POST',
        'tipo' => null,
    ])
</div>
@endsection
