@props(['persona', 'editable' => false])

<div class="aitg-card aitg-card--primary">
    <div class="aitg-card__header">
        <div class="aitg-card__title-wrap">
            <span class="aitg-card__icon aitg-card__icon--primary"><i class="fas fa-user"></i></span>
            <h3 class="aitg-card__title">Datos básicos del aspirante</h3>
        </div>
    </div>
    <div class="aitg-card__body">
        @if($persona)
            <div class="row">
                <div class="col-md-4"><strong>Nombres:</strong> {{ $persona->nombre_completo }}</div>
                <div class="col-md-4"><strong>Documento:</strong> {{ $persona->tipoDocumento->name ?? 'DOC' }} {{ $persona->numero_documento }}</div>
                <div class="col-md-4"><strong>Correo:</strong> {{ $persona->email ?? auth()->user()->email }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Teléfono:</strong> {{ $persona->celular ?? $persona->telefono ?? '—' }}</div>
                <div class="col-md-8"><strong>Dirección:</strong> {{ $persona->direccion ?? '—' }}</div>
            </div>
            <p class="text-muted small mt-2 mb-0">Estos datos provienen de su registro. Actualícelos en su perfil si requiere corregirlos.</p>
        @else
            <p class="text-warning mb-0">No hay datos de persona vinculados a su usuario. Complete su perfil antes de continuar.</p>
        @endif
    </div>
</div>
