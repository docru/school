<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'Superadmin';
        $user->phone = '79002559940';
        $user->saveQuietly();

        $superadmin = Role::create([
            'name' => 'superadmin',
            'display_name' => 'Суперадмин',
        ]);
        $methodologist = Role::create([
            'name' => 'methodologist',
            'display_name' => 'Методист',
        ]);
        $administrator = Role::create([
            'name' => 'administrator',
            'display_name' => 'Администратор',
        ]);
        $teacher = Role::create([
            'name' => 'teacher',
            'display_name' => 'Учитель',
        ]);
        $disciple = Role::create([
            'name' => 'disciple',
            'display_name' => 'Ученик',
        ]);

        $user->addRoles([$superadmin, $methodologist, $administrator, $teacher, $disciple]);
    }
}
