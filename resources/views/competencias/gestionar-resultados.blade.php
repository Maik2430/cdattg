@extends('adminlte::page')

@section('title', 'Gestionar Resultados - Competencia')

@section('css')
    @vite(['resources/css/competencias.css'])
@endsection

@section('content_header')
    <div class="admin-header">
        <div class="admin-header-content">
            <div class="admin-header-left">
                <div class="admin-header-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="admin-header-text">
                    <h1 class="admin-header-title">Gestionar Resultados</h1>
                    <p class="admin-header-subtitle">Competencia: {{ $competencia->codigo }} - {{ Str::limit($competencia->nombre, 50) }}</p>
                </div>
            </div>
            <nav aria-label="breadcrumb" class="admin-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('competencias.index') }}">
                            <i class="fas fa-home me-1"></i>Competencias
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-tasks me-1"></i>Gestionar Resultados
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="main-card">
        <x-session-alerts />
        
        <livewire:competencias.gestionar-resultados :competencia="$competencia" />
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/competencias-index.js'])
@endsection
