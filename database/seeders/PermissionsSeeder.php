<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(config('permissionslist') as $permission) {
            $isExists = Permission::where('name', $permission)->first();
            if(!$isExists) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}