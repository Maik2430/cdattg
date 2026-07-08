<?php

namespace App\Livewire\Aprendices\Concerns;

use App\Models\Aprendiz;

trait HandlesAprendizIndexModalActions
{
    public function openCreateModal(): void
    {
        $this->resetModalVisibility();
        $this->showCreateModal = true;
    }

    public function openEditModal(int|string $aprendizId): void
    {
        $this->selectedAprendiz = $this->findAprendizOrNotify($aprendizId, [
            'persona',
            'fichaCaracterizacion.programaFormacion',
            'fichaCaracterizacion.sede.regional',
        ]);

        if (! $this->selectedAprendiz) {
            return;
        }

        $this->resetModalVisibility();
        $this->showEditModal = true;
    }

    public function openShowModal(int|string $aprendizId): void
    {
        $this->selectedAprendiz = $this->findAprendizOrNotify($aprendizId, [
            'persona',
            'fichaCaracterizacion.programaFormacion.redConocimiento.regional',
            'fichaCaracterizacion.sede',
            'fichaCaracterizacion.ambiente',
        ]);

        if (! $this->selectedAprendiz) {
            return;
        }

        $this->resetModalVisibility();
        $this->showShowModal = true;
    }

    public function openDeleteModal(int|string $aprendizId): void
    {
        $this->selectedAprendiz = $this->findAprendizOrNotify($aprendizId, []);

        if (! $this->selectedAprendiz) {
            return;
        }

        $this->resetModalVisibility();
        $this->showDeleteModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->selectedAprendiz = null;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->selectedAprendiz = null;
    }

    public function closeShowModal(): void
    {
        $this->showShowModal = false;
        $this->selectedAprendiz = null;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->selectedAprendiz = null;
    }

    public function handleCloseModal(): void
    {
        $this->resetModalVisibility();
        $this->selectedAprendiz = null;
    }

    /**
     * @param  list<string>  $with
     */
    private function findAprendizOrNotify(int|string $aprendizId, array $with): ?Aprendiz
    {
        $query = Aprendiz::query();

        if ($with !== []) {
            $query->with($with);
        }

        $aprendiz = $query->find($aprendizId);

        if (! $aprendiz) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Aprendiz no encontrado',
            ]);
        }

        return $aprendiz;
    }

    private function resetModalVisibility(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
    }
}
