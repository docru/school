<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const ROLE = 'home_group_leader';

    /**
     * Роли создаются сидером, но он пересоздаёт базу. Для уже работающих
     * установок роль добавляется миграцией.
     */
    public function up(): void
    {
        if (Role::whereName(self::ROLE)->exists()) {
            return;
        }

        Role::create([
            'name' => self::ROLE,
            'display_name' => 'Лидер домашней группы',
        ]);
    }

    public function down(): void
    {
        $role = Role::whereName(self::ROLE)->first();
        if (!empty($role)) {
            // Снимаем роль с пользователей, иначе останутся висеть строки в role_user
            $role->users()->sync([]);
            $role->delete();
        }
    }
};
