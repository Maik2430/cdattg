<?php

namespace App\Livewire\Instructores\Concerns;

trait HandlesInstructorIndexRenderActions
{
    use HandlesInstructorIndexRenderQueryHelpers;

    public function render()
    {
        return view('livewire.instructores.instructor-index', [
            'instructores' => $this->buildInstructoresList(),
        ]);
    }
}
