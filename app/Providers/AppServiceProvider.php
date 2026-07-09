<?php

namespace App\Providers;

use App\Providers\Concerns\BootsApplicationServices;
use App\Providers\Concerns\RegistersInventarioRepositoryBindings;
use App\Providers\Concerns\RegistersInventarioServiceBindings;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use BootsApplicationServices;
    use RegistersInventarioRepositoryBindings;
    use RegistersInventarioServiceBindings;

    public function register(): void
    {
        $this->registerInventarioRepositoryBindings();
        $this->registerInventarioServiceBindings();
    }

    public function boot(): void
    {
        $this->bootApplicationServices();
    }
}
