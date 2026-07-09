<?php

namespace App\Providers\Concerns;

use App\Models\AsistenciaAprendiz;
use App\Models\Complementarios\AspiranteComplementario;
use App\Observers\AsistenciaAprendizObserver;
use App\Observers\AspiranteComplementarioObserver;
use Illuminate\Pagination\Paginator as PaginationPaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

trait BootsApplicationServices
{
    protected function bootApplicationServices(): void
    {
        setlocale(LC_ALL, 'es_ES', 'es', 'ES', 'es_ES.utf8');
        \Carbon\Carbon::setLocale(config('app.locale', 'es'));
        date_default_timezone_set(config('app.timezone'));
        Schema::defaultStringLength(191);
        PaginationPaginator::useBootstrap();

        AsistenciaAprendiz::observe(AsistenciaAprendizObserver::class);
        AspiranteComplementario::observe(AspiranteComplementarioObserver::class);

        $this->loadBatchMigrations();
        $this->registerGoogleDriveStorage();
    }

    protected function loadBatchMigrations(): void
    {
        $migrationsPath = database_path('migrations');
        $directories = glob($migrationsPath.'/batch_*', GLOB_ONLYDIR) ?: [];
        natsort($directories);

        foreach ($directories as $directory) {
            $this->loadMigrationsFrom($directory);
        }
    }

    protected function registerGoogleDriveStorage(): void
    {
        try {
            Storage::extend('google', function ($config) {
                $options = [];

                if (! empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client;
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);

                if (class_exists(\Google\Service\Drive::class)) {
                    $client->setScopes([\Google\Service\Drive::DRIVE_FILE, \Google\Service\Drive::DRIVE]);
                } else {
                    $driveFileScope = 'https://www.googleapis.com/auth/drive.file';
                    $driveScope = 'https://www.googleapis.com/auth/drive';
                    $client->setScopes([$driveFileScope, $driveScope]);
                }
                $client->setAccessType('offline');
                if (method_exists($client, 'setIncludeGrantedScopes')) {
                    $client->setIncludeGrantedScopes(true);
                }

                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter(
                    $service,
                    $config['folderId'] ?? '/',
                    $options
                );
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            Log::error('Error al registrar driver de Google Drive: '.$e->getMessage());
        }
    }
}
