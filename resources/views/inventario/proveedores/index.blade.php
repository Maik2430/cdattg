@extends('inventario.layouts.base')

@section('title', 'Gestión de Proveedores')

@include('inventario._components.common-css')

@section('content_header')
    <x-page-header
        icon="fas fa-truck"
        title="Gestión de Proveedores"
        subtitle="Administra los proveedores del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.proveedores.create') }}"
                        title="Nuevo Proveedor"
                        icon="fa-plus-circle"
                        permission="CREAR PROVEEDOR"
                    />

                    <x-data-table
                        title="Lista de Proveedores"
                        searchable="true"
                        searchAction="{{ route('inventario.proveedores.index') }}"
                        searchPlaceholder="Buscar proveedor..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '3%'],
                            ['label' => 'Proveedor', 'width' => '11%'],
                            ['label' => 'NIT', 'width' => '7%'],
                            ['label' => 'Email', 'width' => '11%'],
                            ['label' => 'Teléfono', 'width' => '7%'],
                            ['label' => 'Dirección', 'width' => '11%'],
                            ['label' => 'País', 'width' => '8%'],
                            ['label' => 'Departamento', 'width' => '8%'],
                            ['label' => 'Municipio', 'width' => '9%'],
                            ['label' => 'Contacto', 'width' => '9%'],
                            ['label' => 'Contratos', 'width' => '5%'],
                            ['label' => 'Estado', 'width' => '7%'],
                            ['label' => 'Opciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$proveedores->links()"
                    >
                        @forelse ($proveedores as $proveedor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $proveedor->name }}</td>
                                <td>{{ $proveedor->nit ?? 'N/A' }}</td>
                                <td>{{ $proveedor->email ?? 'N/A' }}</td>
                                <td>{{ $proveedor->telefono ?? 'N/A' }}</td>
                                <td>{{ $proveedor->direccion ?? 'N/A' }}</td>
                                <td>{{ $proveedor->pais->pais ?? 'N/A' }}</td>
                                <td>{{ $proveedor->departamento->departamento ?? 'N/A' }}</td>
                                <td>{{ $proveedor->municipio->municipio ?? 'N/A' }}</td>
                                <td>
                                    @if($proveedor->persona)
                                        @if($proveedor->persona->email)
                                            <a href="mailto:{{ $proveedor->persona->email }}" class="text-primary" title="{{ $proveedor->persona->nombre_completo }}">
                                                <i class="fas fa-envelope mr-1"></i>
                                                {{ $proveedor->persona->email }}
                                            </a>
                                        @else
                                            <span class="text-muted" title="{{ $proveedor->persona->nombre_completo }}">N/A</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $proveedor->contratos_convenios_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if($proveedor->estado)
                                        <span class="badge badge-{{ $proveedor->estado->status == 1 ? 'success' : 'danger' }}">
                                            {{ $proveedor->estado->parametro->name ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">SIN ESTADO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.proveedores.show', $proveedor->id) }}"
                                        editUrl="{{ route('inventario.proveedores.edit', $proveedor->id) }}"
                                        deleteUrl="{{ route('inventario.proveedores.destroy', $proveedor->id) }}"
                                        showTitle="Ver proveedor"
                                        editTitle="Editar proveedor"
                                        deleteTitle="Eliminar proveedor"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="12"
                                message="No hay proveedores registrados"
                                icon="fas fa-truck"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $proveedores->firstItem() ?? 0 }} a {{ $proveedores->lastItem() ?? 0 }}
                            de {{ $proveedores->total() }} proveedores
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-confirm-delete-modal />
@endsection

@include('inventario._components.common-footer')

@push('scripts')
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush

