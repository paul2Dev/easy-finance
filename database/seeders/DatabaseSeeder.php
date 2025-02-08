<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin_role = Role::create(['name' => 'admin']);
        User::create([
            'name' => 'Admin',
            'email' => 'paul.maxineanu@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole($admin_role);

    }
}
