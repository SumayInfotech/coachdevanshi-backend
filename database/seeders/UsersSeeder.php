<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Check if role exists
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            $roleId = DB::table('roles')->insertGetId([
                'uuid' => Str::uuid(),
                'name' => 'super_admin',
                'status' => 'Active',
                'created_at' => $now,
                'updated_at' => $now
            ]);

            $superAdminRole = DB::table('roles')->where('id', $roleId)->first();
        }

        $user = DB::table('users')->where('email', 'admin@gmail.com')->first();

        if (!$user) {
            DB::table('users')->insert([
                'uuid' => Str::uuid(),
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'user_type' => 'admin',
                'password' => bcrypt('123456'),
                'role_uuid' => $superAdminRole->uuid,
                'status' => 'Active',
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }
}
