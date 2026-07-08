<?php

namespace Database\Seeders;

use Database\Seeders\Services\DemoUserSeederService;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new DemoUserSeederService())->seedAll();
    }
}
