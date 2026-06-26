@props(['active' => null])

@php
    $items = [
        ['key' => 'inicio', 'label' => 'Inicio', 'icon' => 'fa-home', 'url' => route('home')],
        ['key' => 'programas', 'label' => 'Programas complementarios', 'icon' => 'fa-graduation-cap', 'url' => route('programas-complementarios.index')],
        ['key' => 'convocatorias', 'label' => 'Convocatorias instructores', 'icon' => 'fa-bullhorn', 'url' => route('aitg.convocatorias.publicas.index')],
    ];
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 text-uppercase text-muted font-weight-bold mb-0">Portal público SENA</h2>
    </div>
    <div class="list-group list-group-flush">
        @foreach($items as $item)
            <a href="{{ $item['url'] }}"
               class="list-group-item list-group-item-action d-flex align-items-center {{ $active === $item['key'] ? 'active' : '' }}"
               @if($active === $item['key']) aria-current="page" @endif>
                <i class="fas {{ $item['icon'] }} mr-2"></i>
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</div>
