@props(['title', 'subtitle', 'breadcrumb' => []])

{{-- Encabezado AITG alineado al wireframe de alta fidelidad --}}
<section class="content-header aitg-page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="aitg-page-header__title">{{ $title }}</h1>
                <p class="aitg-page-header__subtitle">{{ $subtitle }}</p>
            </div>
            <div class="col-md-5">
                @if(! empty($breadcrumb))
                    <x-breadcrumb :items="$breadcrumb" />
                @endif
            </div>
        </div>
    </div>
</section>
