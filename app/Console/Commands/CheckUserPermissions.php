<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\CheckUserPermissions\HandlesCheckUserPermissionsDisplayActions;
use App\Console\Commands\Concerns\CheckUserPermissions\HandlesCheckUserPermissionsGroupingHelpers;
use Illuminate\Console\Command;

class CheckUserPermissions extends Command
{
    use HandlesCheckUserPermissionsDisplayActions;
    use HandlesCheckUserPermissionsGroupingHelpers;

    protected $signature = 'user:check-permissions {userId? : ID del usuario a verificar}';

    protected $description = 'Verifica los permisos de un usuario específico o lista todos los usuarios con sus roles';

    public function handle(): int
    {
        $userId = $this->argument('userId');

        if ($userId) {
            $this->showUserPermissions($userId);
        } else {
            $this->listAllUsers();
        }

        return 0;
    }
}
