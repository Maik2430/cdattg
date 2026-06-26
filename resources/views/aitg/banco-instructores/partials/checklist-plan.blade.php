@props(['plan'])

@if($plan->checklist->isNotEmpty())
<div class="aitg-card aitg-card--info mb-4">
    <div class="aitg-card__header">
        <div class="aitg-card__title-wrap">
            <span class="aitg-card__icon aitg-card__icon--info"><i class="fas fa-tasks"></i></span>
            <h3 class="aitg-card__title">Checklist documental del plan</h3>
        </div>
    </div>
    <div class="aitg-card__body">
        <p class="text-muted small">
            Criterios evaluables del plan. En evaluación cada ítem obligatorio tiene el mismo peso:
            <strong>100 ÷ {{ $plan->checklist->count() }}</strong> = {{ number_format(100 / max(1, $plan->checklist->count()), 2) }}%.
        </p>
        <ul class="mb-0">
            @foreach($plan->checklist as $item)
                <li><strong>Checklist {{ $item->consecutivo }}:</strong> {{ $item->descripcion_criterio }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
