<?php

namespace App\Livewire\RedConocimiento\Concerns;

use App\Models\RedConocimiento;
use Illuminate\Database\Eloquent\Builder;

trait HandlesRedConocimientoIndexRenderQueryHelpers
{
    private function buildRedesConocimientoQuery(): Builder
    {
        $query = RedConocimiento::with(['regional']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhereHas('regional', function ($subQuery) {
                        $subQuery->where('nombre', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter === '1');
        }

        if ($this->regionalFilter !== '') {
            $query->where('regionals_id', $this->regionalFilter);
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }
}
