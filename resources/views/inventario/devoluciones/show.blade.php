@extends('inventario.layouts.base')

@section('title', 'Detalle de Devolución')

@include('inventario._components.common-css')

@section('content_header')
    <x-page-header
        icon="fas fa-undo"
        title="Detalle de Devolución"
        subtitle="Información completa de la devolución registrada"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Devoluciones', 'url' => route('inventario.devoluciones.index')],
            ['label' => 'Detalle', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            @include('components.session-alerts')

            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i>
                                Información de la Devolución #{{ $devolucion->id }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $detalleOrden = $devolucion->detalleOrden;
                                $producto = $detalleOrden->producto ?? null;
                                $productoNombre = $producto?->name ?? 'N/A';
                                $productoDescripcion = $producto?->descripcion ?? '';
                                $orden = $detalleOrden->orden ?? null;
                            @endphp

                            {{-- Información del Producto y Orden --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3">
                                                <i class="fas fa-box"></i> Producto
                                            </h6>
                                            <p class="mb-1">
                                                <strong>{{ $productoNombre }}</strong>
                                            </p>
                                            @if($productoDescripcion)
                                            <small class="text-muted">{{ $productoDescripcion }}</small>
                                            @endif
                                            @if($producto && $producto->codigo_barras)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-barcode"></i> Código: {{ $producto->codigo_barras }}
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3">
                                                <i class="fas fa-file-invoice"></i> Orden
                                            </h6>
                                            <p class="mb-1">
                                                <strong>Orden #{{ $orden->id ?? 'N/A' }}</strong>
                                            </p>
                                            @if($orden)
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i>
                                                Fecha préstamo: {{ $orden->created_at ? $orden->created_at->format('d/m/Y') : 'N/A' }}
                                            </small>
                                            @if($orden->fecha_devolucion)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check"></i>
                                                Fecha devolución esperada: {{ $orden->fecha_devolucion->format('d/m/Y') }}
                                            </small>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Información de Cantidades --}}
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6>Cantidad Prestada</h6>
                                            <h3>{{ $detalleOrden->cantidad ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6>Cantidad Devuelta</h6>
                                            <h3>{{ $devolucion->cantidad_devuelta }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h6>Total Devuelto</h6>
                                            <h3>{{ $detalleOrden->getCantidadDevuelta() ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h6>Pendiente</h6>
                                            <h3>{{ $detalleOrden->getCantidadPendiente() ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Información de la Devolución --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3">
                                                <i class="fas fa-calendar-alt"></i> Fechas
                                            </h6>
                                            <p class="mb-2">
                                                <strong>Fecha de Devolución:</strong><br>
                                                <span class="badge badge-primary">
                                                    {{ $devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('d/m/Y') : 'N/A' }}
                                                </span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>Registrado el:</strong><br>
                                                <small class="text-muted">
                                                    {{ $devolucion->created_at ? $devolucion->created_at->format('d/m/Y H:i') : 'N/A' }}
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3">
                                                <i class="fas fa-user"></i> Usuario
                                            </h6>
                                            <p class="mb-2">
                                                <strong>Registrado por:</strong><br>
                                                <span>{{ $devolucion->userCreate->name ?? 'N/A' }}</span>
                                            </p>
                                            @if($devolucion->userUpdate && $devolucion->userUpdate->id !== $devolucion->userCreate->id)
                                            <p class="mb-2">
                                                <strong>Actualizado por:</strong><br>
                                                <span>{{ $devolucion->userUpdate->name ?? 'N/A' }}</span>
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Estado y Observaciones --}}
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3">
                                                <i class="fas fa-info-circle"></i> Estado y Observaciones
                                            </h6>
                                            <div class="mb-3">
                                                <strong>Estado:</strong>
                                                @if($devolucion->cierra_sin_stock)
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> Cierre sin stock
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Stock restaurado
                                                    </span>
                                                @endif
                                            </div>
                                            @if($devolucion->observaciones)
                                            <div>
                                                <strong>Observaciones:</strong>
                                                <p class="text-muted mt-2">{{ $devolucion->observaciones }}</p>
                                            </div>
                                            @else
                                            <div>
                                                <strong>Observaciones:</strong>
                                                <p class="text-muted mt-2">Sin observaciones</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="row">
                                <div class="col-12">
                                    <a
                                        href="{{ route('inventario.devoluciones.index') }}"
                                        class="btn btn-secondary"
                                    >
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                    <a
                                        href="{{ route('inventario.devoluciones.historial') }}"
                                        class="btn btn-info"
                                    >
                                        <i class="fas fa-history"></i> Ver Historial
                                    </a>
                                    @if($orden)
                                    <a
                                        href="{{ route('inventario.ordenes.show', ['orden' => $orden->id, 'ref' => url()->current()]) }}"
                                        class="btn btn-primary"
                                    >
                                        <i class="fas fa-file-invoice"></i> Ver Orden
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@include('inventario._components.common-footer')
