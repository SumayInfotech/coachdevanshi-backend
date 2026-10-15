<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super_admin',
            'admin',
            'manager',
        ];

        $now = Carbon::now();

        
        foreach ($roles as $name) {
            $existing = DB::table('roles')->where('name', $name)->first();

            if (!$existing) {
                DB::table('roles')->insert([
                    'uuid' => Str::uuid(),
                    'name' => $name,
                    'status' => 'Active',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }
}
