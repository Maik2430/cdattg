@extends('adminlte::page')

@section('css')
    @vite(['resources/css/Asistencia/caracter_selecter.css'])
@endsection

@php
    // Log para debugging en la vista
    \Illuminate\Support\Facades\Log::info('=== DEBUG VISTA CARACTER_SELECTER ===');
    \Illuminate\Support\Facades\Log::info('instructorFicha existe: ' . (isset($instructorFicha) ? 'SI' : 'NO'));
    if (isset($instructorFicha)) {
        \Illuminate\Support\Facades\Log::info('instructorFicha es null: ' . ($instructorFicha === null ? 'SI' : 'NO'));
        \Illuminate\Support\Facades\Log::info('instructorFicha isEmpty: ' . ($instructorFicha->isEmpty() ? 'SI' : 'NO'));
        \Illuminate\Support\Facades\Log::info('instructorFicha count: ' . $instructorFicha->count());
    }
    \Illuminate\Support\Facades\Log::info('=== FIN DEBUG VISTA ===');
@endphp

@section('content_header')
    <x-page-header 
        icon="fa-home" 
        title="Fichas de formación"
        subtitle="Gestión de fichas de formación"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin') , 'icon' => 'fa-home'], ['label' => 'Fichas de formación', 'icon' => 'fa-fw fa-paint-brush', 'active' => true]]"
    />
@endsection

@section('content')
    <x-session-alerts />
    
    <section class="content">
        <div class="container-fluid">
            @if(empty($instructorFicha) || $instructorFicha->isEmpty())
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                                <h3 class="mt-3 text-muted">No tienes fichas asignadas</h3>
                                <p class="text-muted">No se encontraron fichas de caracterización asignadas a tu cuenta.</p>
                                <p class="text-muted">Contacta al administrador para que te asigne las fichas correspondientes.</p>
                                <a href="{{ route('verificarLogin') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-home mr-2"></i>Volver al inicio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach ($instructorFicha as $caracterizacion)
                    <div class="col-md-4 mb-4">
                        <div
                            class="card h-100 shadow-sm border-0 rounded-lg overflow-hidden transition-all hover:shadow-lg">
                            <div class="card-header bg-gradient-primary text-white py-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book fa-lg mr-2"></i>
                                    <h3 class="card-title mb-0 font-weight-bold">{{ $caracterizacion->ficha->ficha }} -
                                        {{ $caracterizacion->ficha->programaFormacion->nombre }}
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body py-3">
                            {{-- Comentado: Obtener próxima clase (causaba errores)
                            @php
                                $proximaClaseFormacion = $caracterizacion->obtenerProximaClase();
                            @endphp
                            --}}
                                {{-- Comentado: Sección de Competencia
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-tasks text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Competencia:</b></h6>
                                    </div>
                                    <p class="ml-4 text-muted">
                                        {{ $caracterizacion->ficha->programaFormacion->competenciaActual()->nombre ?? 'No asignada' }}
                                    </p>
                                </div>
                                --}}
                                {{-- Comentado: Sección de RAP (Resultado de Aprendizaje)
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-list-ol text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>RAP:</b></h6>
                                    </div>
                                    <p class="ml-4 text-muted">
                                        {{ $caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()->nombre ?? 'No asignado' }}
                                    </p>
                                </div>
                                --}}

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-graduation-cap text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Modalidad:</b></h6>
                                    </div>
                                    <div class="d-flex align-items-center ml-4 text-muted">
                                        <span>{{ $caracterizacion->ficha->modalidadFormacion->name ?? 'No especificada' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="far fa-sun text-primary mr-2"></i>
                                            <h6 class="mb-0"><b>Jornada:</b></h6>
                                        </div>
                                        <p class="ml-4 text-muted">{{ $caracterizacion->ficha->jornadaFormacion->parametro->name ?? 'No asignada' }}
                                        </p>
                                    </div>
                                    {{-- Comentado: Sección de Horario de formación (causaba error si $proximaClaseFormacion es null)
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="far fa-clock text-primary mr-2"></i>
                                            <h6 class="mb-0"><b>Horario de formación:</b></h6>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <p class="ml-4 mb-0 text-muted">
                                                {{ Carbon\Carbon::parse($proximaClaseFormacion['hora_inicio'])->format('g:i A') }}
                                                -
                                                {{ Carbon\Carbon::parse($proximaClaseFormacion['hora_fin'])->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    --}}
                                </div>
                                {{-- Comentado: Sección de Dias de formación
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="far fa-calendar-alt text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Dias de formación:</b></h6>
                                    </div>
                                    @php
                                        $dias = $caracterizacion->instructorFichaDias;
                                    @endphp
                                    <div class="d-flex ml-4" style="gap: 0.5rem;">
                                        @foreach ($dias as $dia)
                                            <div class="border rounded text-center px-2 py-1"
                                                style="min-width: 60px; background: {{ $dia->dia_id == $proximaClaseFormacion['dia_id'] ? '#007bff' : '#f8f9fa' }}; color: {{ $dia->dia_id == $proximaClaseFormacion['dia_id'] ? '#fff' : '#6c757d' }};">
                                                {{ substr($diasFormacion[$dia->dia_id - 12]['name'], 0, 3) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                --}}
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Lugar de formación:</b></h6>
                                    </div>
                                    <div class="d-flex align-items-center ml-4 text-muted" style="gap: 0.5rem;">
                                        <span>{{ $caracterizacion->ficha->ambiente->piso->bloque->sede->sede ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>{{ $caracterizacion->ficha->ambiente->piso->bloque->bloque ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>{{ $caracterizacion->ficha->ambiente->piso->piso ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>{{ $caracterizacion->ficha->ambiente->title ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-primary btn-block py-2 font-weight-bold" data-bs-toggle="modal" data-bs-target="#actividadesModal">
                                            <i class="fas fa-clipboard-check mr-1"></i> Actividades
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        @php
                                            $jornadaNombre = $caracterizacion->ficha->jornadaFormacion?->parametro?->name ?? '';
                                        @endphp
                                        @if($jornadaNombre)
                                            <button type="button" class="btn btn-success btn-block py-2 font-weight-bold" data-bs-toggle="modal" data-bs-target="#novedadesModal">
                                                <i class="fas fa-newspaper mr-1"></i> Novedades
                                            </button>
                                        @else
                                            <button class="btn btn-success btn-block py-2 font-weight-bold" disabled title="Jornada no asignada">
                                                <i class="fas fa-newspaper mr-1"></i> Novedades
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="row g-2 mt-2">
                                    <div class="col-12">
                                        <a href="#" 
                                           data-tomar-asistencia="true"
                                           data-caracterizacion-id="{{ $caracterizacion->id }}"
                                           data-ficha-id="{{ $caracterizacion->ficha_id }}"
                                           class="btn btn-info btn-block py-2 font-weight-bold">
                                            <i class="fas fa-qrcode mr-1"></i> Tomar asistencia QR
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Modal de Actividades -->
    <div class="modal fade" id="actividadesModal" tabindex="-1" aria-labelledby="actividadesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="actividadesModalLabel">
                        <i class="fas fa-clipboard-check mr-2"></i>Actividades
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-tools fa-3x text-primary mb-3"></i>
                    </div>
                    <h4 class="text-primary mb-3">Función a Futuro</h4>
                    <p class="text-muted">
                        Esta funcionalidad estará disponible próximamente. Permitirá gestionar y registrar 
                        las actividades académicas y prácticas de los aprendices.
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Próximamente:</strong> Registro de actividades, seguimiento de tareas, 
                        evaluación de desempeño y más.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Novedades -->
    <div class="modal fade" id="novedadesModal" tabindex="-1" aria-labelledby="novedadesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="novedadesModalLabel">
                        <i class="fas fa-newspaper mr-2"></i>Novedades
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-newspaper fa-3x text-success mb-3"></i>
                    </div>
                    <h4 class="text-success mb-3">Función a Futuro</h4>
                    <p class="text-muted">
                        Esta funcionalidad estará disponible próximamente. Permitirá registrar y gestionar 
                        las novedades, incidencias y reportes de los aprendices.
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Próximamente:</strong> Reporte de novedades, control de inasistencias, 
                        seguimiento de comportamientos y más.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    <!-- Modal para crear evidencia -->
    <div class="modal fade" id="crearEvidenciaModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt mr-2"></i> Crear Nueva Evidencia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="crearEvidenciaForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreEvidencia" class="form-label fw-bold">
                                <i class="fas fa-edit mr-1"></i> Nombre de la Evidencia
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nombreEvidencia" 
                                   name="nombre" 
                                   placeholder="Ej: Actividad Práctica 1, Evaluación Diagnóstica, etc."
                                   required
                                   maxlength="255">
                            <small class="text-muted">Ingrese un nombre descriptivo para la evidencia de aprendizaje</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-info-circle mr-1"></i> Información
                            </label>
                            <div class="alert alert-info">
                                <small>
                                    <strong>Ficha:</strong> {{ $caracterizacion->ficha->ficha ?? '' }} - {{ $caracterizacion->ficha->programaFormacion->nombre ?? '' }}<br>
                                    <strong>Instructor:</strong> @if($caracterizacion->instructor && $caracterizacion->instructor->persona){{ $caracterizacion->instructor->persona->getNombreCompletoAttribute() }}@else No asignado @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="guardarEvidenciaBtn">
                            <i class="fas fa-save mr-1"></i> Crear Evidencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar clic en botones de "Tomar asistencia QR"
            const tomarAsistenciaBtns = document.querySelectorAll('[data-tomar-asistencia]');
            const modal = document.getElementById('crearEvidenciaModal');
            const form = document.getElementById('crearEvidenciaForm');
            
            if (tomarAsistenciaBtns.length > 0 && modal && form) {
                tomarAsistenciaBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Obtener datos de la caracterización
                        const caracterizacionId = this.dataset.caracterizacionId;
                        const fichaId = this.dataset.fichaId;
                        
                        // Guardar datos en el formulario
                        form.dataset.caracterizacionId = caracterizacionId;
                        form.dataset.fichaId = fichaId;
                        
                        // Mostrar modal - Método alternativo
                        const modalElement = document.getElementById('crearEvidenciaModal');
                        
                        // Intentar con Bootstrap 5
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            const modal = new bootstrap.Modal(modalElement);
                            modal.show();
                        } else {
                            // Método alternativo: mostrar manualmente
                            modalElement.classList.add('show');
                            modalElement.style.display = 'block';
                            document.body.classList.add('modal-open');
                            
                            // Crear backdrop
                            const backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade show';
                            document.body.appendChild(backdrop);
                        }
                    });
                });
                
                // Manejar envío del formulario
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    console.log('=== DEBUG JAVASCRIPT STORE EVIDENCIA ===');
                    console.log('Form dataset:', form.dataset);
                    console.log('Caracterizacion ID:', form.dataset.caracterizacionId);
                    console.log('Ficha ID:', form.dataset.fichaId);
                    
                    const submitBtn = document.getElementById('guardarEvidenciaBtn');
                    const originalText = submitBtn.innerHTML;
                    
                    // Deshabilitar botón y mostrar loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Creando...';
                    
                    const formData = new FormData(form);
                    formData.append('caracterizacion_id', form.dataset.caracterizacionId);
                    formData.append('ficha_id', form.dataset.fichaId);
                    
                    console.log('FormData a enviar:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key + ':', value);
                    }
                    
                    fetch('{{ route('evidencias.store.simple') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', [...response.headers.entries()]);
                        
                        // Verificar si la respuesta es OK y tiene contenido
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        return response.text().then(text => {
                            console.log('Response text raw:', text);
                            try {
                                return JSON.parse(text);
                            } catch (parseError) {
                                console.error('Error parsing JSON:', parseError);
                                console.error('Response text that failed to parse:', text);
                                throw new Error('La respuesta no es un JSON válido');
                            }
                        });
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        
                        if (data.success) {
                            // Redirigir directamente a la vista de asistencia QR con la nueva evidencia
                            const redirectUrl = `{{ route('asistence.caracterSelected.withEvidencia', ['caracterizacion' => ':caracterizacion', 'evidencia_id' => ':evidencia'])}}`
                                .replace(':caracterizacion', form.dataset.caracterizacionId)
                                .replace(':evidencia', data.evidencia_id);
                            
                            console.log('Redirecting to:', redirectUrl);
                            console.log('Caracterizacion ID:', form.dataset.caracterizacionId);
                            console.log('Evidencia ID:', data.evidencia_id);
                            
                            // Redirigir inmediatamente sin cerrar el modal
                            window.location.href = redirectUrl;
                        } else {
                            console.error('Error en respuesta:', data);
                            alert('Error al crear la evidencia: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error completo en fetch:', error);
                        console.error('Error name:', error.name);
                        console.error('Error message:', error.message);
                        console.error('Error stack:', error.stack);
                        alert('Error de comunicación al crear la evidencia: ' + error.message);
                    })
                    .finally(() => {
                        // Restaurar botón
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        console.log('=== FIN DEBUG JAVASCRIPT ===');
                    });
                });
            }
            
            // Manejar modales de Actividades y Novedades (sin Bootstrap)
            const actividadesBtn = document.querySelector('[data-bs-target="#actividadesModal"]');
            const actividadesModal = document.getElementById('actividadesModal');
            
            if (actividadesBtn && actividadesModal) {
                actividadesBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    showModal('actividadesModal');
                });
            }
            
            // Modal de Novedades
            const novedadesBtn = document.querySelector('[data-bs-target="#novedadesModal"]');
            const novedadesModal = document.getElementById('novedadesModal');
            
            if (novedadesBtn && novedadesModal) {
                novedadesBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    showModal('novedadesModal');
                });
            }
            
            // Función para mostrar modal
            function showModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    // Mostrar modal
                    modal.classList.add('show');
                    modal.style.display = 'block';
                    document.body.classList.add('modal-open');
                    
                    // Crear backdrop
                    let backdrop = document.querySelector('.modal-backdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                    
                    // Cerrar modal al hacer clic en el backdrop
                    backdrop.addEventListener('click', function() {
                        hideModal(modalId);
                    });
                    
                    // Cerrar modal con botones de cierre
                    const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
                    closeButtons.forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            hideModal(modalId);
                        });
                    });
                }
            }
            
            // Función para ocultar modal
            function hideModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    
                    // Eliminar backdrop
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }
            
            // Cerrar modales con tecla Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modals = document.querySelectorAll('.modal.show');
                    modals.forEach(function(modal) {
                        hideModal(modal.id);
                    });
                }
            });
        });
    </script>
@endsection
