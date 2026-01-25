@extends('adminlte::page')

@section('title', $resultadoAprendizaje->codigo . ' - ' . $resultadoAprendizaje->nombre)

@section('css')
    @vite(['resources/css/competencias.css'])
@endsection

@section('content_header')
    <div class="admin-header">
        <div class="admin-header-content">
            <div class="admin-header-left">
                <div class="admin-header-icon" style="opacity: 0.6; font-size: 1.2rem;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="admin-header-text">
                    <h1 class="admin-header-title">{{ $resultadoAprendizaje->codigo }} · {{ $resultadoAprendizaje->nombre }}</h1>
                    <p class="admin-header-subtitle">
                        {{ $resultadoAprendizaje->programaFormacion->red->nombre ?? 'Sin red' }} · 
                        {{ $resultadoAprendizaje->programaFormacion->nivel->nombre ?? 'Sin nivel' }} · 
                        {{ formatear_horas($resultadoAprendizaje->duracion) }} horas · 
                        <span class="badge badge-{{ $resultadoAprendizaje->status ? 'success' : 'danger' }}">
                            {{ $resultadoAprendizaje->status ? 'Activo' : 'Inactivo' }}
                        </span>
                    </p>
                </div>
            </div>
            <nav aria-label="breadcrumb" class="admin-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('verificarLogin') }}">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('resultados-aprendizaje.index') }}">
                            <i class="fas fa-graduation-cap me-1"></i>Resultados de Aprendizaje
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-link me-1"></i>{{ $resultadoAprendizaje->codigo }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="page-container">
        <div class="main-card">
            <x-session-alerts />
            
            <!-- Componente Livewire para manejar acciones y datos -->
            <livewire:resultados-aprendizaje.gestionar-competencias-handler />
        </div>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/resultados-aprendizaje-index.js'])
@endsection

@if(session('success'))
    <script>
        $(document).ready(function() {
            Livewire.dispatch('notify', {
                type: 'success',
                message: '{{ session('success') }}'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        $(document).ready(function() {
            Livewire.dispatch('notify', {
                type: 'error',
                message: '{{ session('error') }}'
            });
        });
    </script>
@endif

<style>
/* Header mejorado - Guía de acción clara */
.page-header-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.header-left {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.header-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #eef2ff;
    color: #4f46e5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.header-text h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
}

.header-subtitle {
    margin-top: 2px;
    font-size: 14px;
    color: #374151;
}

.header-meta {
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.header-meta .dot {
    color: #d1d5db;
}

.header-actions .btn {
    white-space: nowrap;
}

.btn-outline-secondary {
    color: #6b7280;
    border-color: #d1d5db;
    background: #ffffff;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-outline-secondary:hover {
    background: #f9fafb;
    color: #374151;
    border-color: #9ca3af;
}

/* Contenedor con ancho máximo para reducir vacío */
.page-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Franja de contexto MUY sutil */
.context-bar {
    font-size: 13px;
    color: #6b7280;
    margin: 8px 0 16px;
    padding: 0 4px;
}

.context-bar span {
    margin-right: 6px;
}

/* Acción guía arriba */
.page-hint {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 12px;
}

/* Columnas con identidad mínima */
.competencias-panel {
    display: flex;
    flex-direction: column;
    height: 100%;
    border-radius: 8px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

/* Diferenciar columnas - Asignadas más estables */
.competencias-panel:first-child {
    background: #f9fafb;
    box-shadow: none;
}

/* Disponibles más accionables */
.competencias-panel:last-child {
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.competencias-header {
    padding: 12px 16px;
    font-weight: 600;
    background: #f1f3f4;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
    font-size: 14px;
}

.competencias-body {
    flex: 1;                 /* 🔥 CLAVE */
    overflow-y: auto;        /* 🔥 CLAVE */
    max-height: 420px;       /* 🔥 CLAVE */
    padding: 8px 0;
}

/* Scroll bonito (opcional) */
.competencias-body::-webkit-scrollbar {
    width: 6px;
}

.competencias-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.competencias-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Item con separadores suaves - Ritmo visual */
.competencia-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px dashed #e5e7eb;
    transition: background-color 0.2s ease;
    overflow: visible; /* ✅ Evita recorte de hovers */
}

.competencia-item:hover {
    background-color: #f9fafb;
}

.competencia-item:last-child {
    border-bottom: none;
}

.competencia-code {
    font-size: 12px;
    color: #6b7280;
    margin-right: 8px;
    font-weight: 500;
    min-width: 60px;
}

.competencia-name {
    font-size: 14px;
    color: #374151;
    flex: 1;
}

/* Botones de acción - Tamaño congelado ERP limpio */
.btn-action {
    font-size: 12px;
    padding: 4px 12px; /* ✅ Padding fijo */
    border: 1px solid;
    background: transparent;
    cursor: pointer;
    transition: background-color 0.15s ease, color 0.15s ease; /* ✅ Solo color y bg */
    border-radius: 4px;
    min-width: 80px; /* ✅ Ancho mínimo para evitar saltos */
    text-align: center;
    white-space: nowrap; /* ✅ Evita saltos de línea */
}

.btn-add {
    color: #059669;
    border-color: #059669;
    font-weight: 500;
}

.btn-add:hover {
    background-color: #ecfdf5;
    color: #059669;
}

.btn-remove {
    color: #dc2626;
    border-color: #dc2626;
    min-width: 70px; /* ✅ Un poco más pequeño para Quitar */
}

.btn-remove:hover {
    background-color: #dc2626;
    color: white;
}

/* Empty states explícitos - CLAVE UX */
.empty-state {
    text-align: center;
    color: #9ca3af;
    font-size: 14px;
    padding: 24px 16px;
}

.empty-state p {
    margin: 0 0 4px 0;
    font-weight: 500;
}

.empty-state small {
    font-size: 12px;
    opacity: 0.8;
}

/* Empty hint elegante - CLAVE para sensación de soledad */
.empty-hint {
    margin-top: 12px;
    color: #9ca3af;
    font-size: 13px;
    text-align: center;
    padding: 0 16px 16px;
}
</style>
