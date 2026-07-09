<?php

namespace App\Repositories\Concerns\Configuracion;

trait HandlesConfiguracionRepositoryCacheActions
{
    public function invalidarCache(): void
    {
        $this->flushCache();
    }

    public function invalidarCacheFichas(): void
    {
        $this->forgetCache('fichas.activas');
    }

    public function invalidarCacheRegionales(): void
    {
        $this->forgetCache('regionales.activas');
    }
}
