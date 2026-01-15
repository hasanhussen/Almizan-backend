<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run()
    {

   
        // ================= Permissions =================

        $permissions = [
            // Users
            'add user',
            'edit user',
            'delete user',
            'ban user',
            'unban user',
            'expel user from exam',
            'allow exam retake',

            // Exams
            'create exam',
            'edit exam',
            'start exam',
            'close exam',
            'end exam',
            'view results',
            'edit result',
            'generate codes',

            // Academic
            'manage subjects',
            'manage questions',
            'manage answers',
            'manage specialties',

            // Roles
            'assign roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ================= Roles =================

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // ================= Admin permissions =================
        $adminRole->syncPermissions([
            // Users
            'add user',
            'edit user',
            'delete user',
            'ban user',
            'unban user',
            'expel user from exam',
            'allow exam retake',

            // Exams
            'create exam',
            'edit exam',
            'start exam',
            'close exam',
            'end exam',
            'view results',
            'edit result',
            'generate codes',

            // Academic
            'manage subjects',
            'manage questions',
            'manage answers',
            'manage specialties',

            // Roles
            'assign roles',
        ]);

        // ================= Supervisor permissions =================
        $supervisorRole->syncPermissions([
            // Users (without delete & roles)
            'add user',
            'edit user',
            'ban user',
            'unban user',
            'expel user from exam',
            'allow exam retake',

            // Exams
            'create exam',
            'edit exam',
            'start exam',
            'close exam',
            'end exam',
            'view results',
            'generate codes',

            // Academic (NO questions / answers / specialties)
            'manage subjects',
        ]);

        // ================= Teacher permissions =================
        $teacherRole->syncPermissions([
            'create exam',
            'edit exam',
            'manage questions',
            'manage answers',
            'edit result'
        ]);

        // ================= Student =================
        // No permissions needed (logic-based access)

        // ================= Admin User =================

        $adminEmail = 'admin@example.com';

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // غيرها بعدين
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

         $roles = Role::where('name', '!=', 'admin')->pluck('name')->toArray(); // الأدوار غير الـ admin

    $users = User::factory(10)->create();

    // إعطاء كل مستخدم دور عشوائي
    $users->each(function ($user) use ($roles) {
        $user->assignRole($roles[array_rand($roles)]);
    });


        $this->command->info('Roles, permissions, and admin user created successfully.');
    }
}
