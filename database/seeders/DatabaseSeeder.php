<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            DefaultSettingsSeeder::class,
            AdminUserSeeder::class,
            CarMakesAndColorsSeeder::class,
            TraditionalTemplateSeeder::class,
            TraditionalInspectionSeeder::class,
            AutoScoreInspectionSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}