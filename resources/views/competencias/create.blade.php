@if (!isset($inAccordion) || !$inAccordion)
    @section('content')
        <section class="content mt-4">
            <div class="container-fluid">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('competencias.index') }}">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>

                        <div class="card shadow-sm no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-plus-circle mr-2"></i>Nueva Competencia
                                </h5>
                            </div>
                            <div class="card-body">
                                @include('competencias.create', ['inAccordion' => false])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endsection
@else
    <div class="card-body">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('competencias.store') }}">
            @csrf

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-info-circle mr-1"></i> Información de la Competencia
                </div>

                <div class="form-group mb-3">
                    <label for="descripcion" class="form-label font-weight-bold">
                        Norma / Unidad de competencia <span class="text-danger">*</span>
                    </label>
                    <textarea
                        name="descripcion"
                        id="descripcion"
                        rows="3"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Describa la norma o unidad de competencia"
                        required
                    >{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Máximo 1000 caracteres.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="codigo" class="form-label font-weight-bold">
                                Código de norma de competencia laboral <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="codigo"
                                id="codigo"
                                class="form-control @error('codigo') is-invalid @enderror"
                                value="{{ old('codigo') }}"
                                placeholder="Ej: NCL-1234"
                                maxlength="50"
                                required
                            >
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="duracion" class="form-label font-weight-bold">
                                Duración máxima de la competencia (horas) <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                name="duracion"
                                id="duracion"
                                class="form-control @error('duracion') is-invalid @enderror"
                                value="{{ old('duracion') }}"
                                placeholder="Ej: 120"
                                min="1"
                                required
                            >
                            @error('duracion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="nombre" class="form-label font-weight-bold">
                        Nombre de la competencia <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre') }}"
                        placeholder="Ej: Aplicar normas de calidad en el desarrollo de software"
                        maxlength="255"
                        required
                    >
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="programas" class="form-label font-weight-bold">
                        Programas de formación <span class="text-danger">*</span>
                    </label>
                    <select
                        name="programas[]"
                        id="programas"
                        class="form-control select2 @error('programas') is-invalid @enderror @error('programas.*') is-invalid @enderror"
                        multiple
                        data-placeholder="Seleccione los programas de formación"
                        required
                    >
                        @foreach($programas as $programa)
                            <option value="{{ $programa->id }}" {{ collect(old('programas', []))->contains($programa->id) ? 'selected' : '' }}>
                                {{ $programa->codigo }} - {{ $programa->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('programas')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('programas.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Puede seleccionar uno o varios programas donde aplica esta competencia.
                    </small>
                </div>
            </div>

            <div class="alert alert-info">
                Las competencias se podrán modificar posteriormente, incluyendo la eliminación de vínculos con los programas seleccionados.
            </div>

            <hr class="mt-4">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-light mr-2" data-toggle="collapse" data-target="#collapseCrearCompetencia" aria-expanded="false">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Guardar
                </button>
            </div>
        </form>
    </div>
@endif

@if (!isset($inAccordion) || !$inAccordion)
    @section('js')
        @vite(['resources/js/pages/competencias-form.js'])
        <style>
            /* Estilos inline para asegurar que el contorno sea visible */
            .select2-container--bootstrap-5 .select2-selection,
            .select2-container--bootstrap-5 .select2-selection--multiple {
                border: 1px solid #ced4da !important;
            }
            
            .select2-container--bootstrap-5.select2-container--focus .select2-selection,
            .select2-container--bootstrap-5.select2-container--focus .select2-selection--multiple {
                border-color: #80bdff !important;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            }
            
            .select2-container.is-invalid .select2-selection,
            .select2-container.is-invalid .select2-selection--multiple,
            .select2-container--bootstrap-5.is-invalid .select2-selection,
            .select2-container--bootstrap-5.is-invalid .select2-selection--multiple {
                border: 1px solid #dc3545 !important;
                border-color: #dc3545 !important;
            }
        </style>
    @endsection
@else
    @push('js')
        @vite(['resources/js/pages/competencias-form.js'])
        <style>
            /* Estilos inline para asegurar que el contorno sea visible */
            .select2-container--bootstrap-5 .select2-selection,
            .select2-container--bootstrap-5 .select2-selection--multiple {
                border: 1px solid #ced4da !important;
            }
            
            .select2-container--bootstrap-5.select2-container--focus .select2-selection,
            .select2-container--bootstrap-5.select2-container--focus .select2-selection--multiple {
                border-color: #80bdff !important;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            }
            
            .select2-container.is-invalid .select2-selection,
            .select2-container.is-invalid .select2-selection--multiple,
            .select2-container--bootstrap-5.is-invalid .select2-selection,
            .select2-container--bootstrap-5.is-invalid .select2-selection--multiple {
                border: 1px solid #dc3545 !important;
                border-color: #dc3545 !important;
            }
        </style>
    @endpush
@endif
