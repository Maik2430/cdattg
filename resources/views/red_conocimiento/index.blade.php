@extends('adminlte::page')

@section('title', 'Redes de Conocimiento')

@section('css')
    @vite(['resources/css/red-conocimiento.css'])
@endsection

@section('content_header')
    <div class="admin-header">
        <div class="admin-header-content">
            <div class="admin-header-left">
                <div class="admin-header-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div class="admin-header-text">
                    <h1 class="admin-header-title">Redes de Conocimiento</h1>
                    <p class="admin-header-subtitle">Gestiona y administra las redes de conocimiento del SENA</p>
                </div>
            </div>
            <nav aria-label="breadcrumb" class="admin-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('verificarLogin') }}">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-network-wired me-1"></i>Redes
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="main-card">
        <x-session-alerts />
        
        <livewire:red-conocimiento.red-conocimiento-index />
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/red-conocimiento-index.js'])
@endsection
