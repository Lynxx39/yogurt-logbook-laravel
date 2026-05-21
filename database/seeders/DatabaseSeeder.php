<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $exists = DB::table('users')->where('username', 'guru')->first();
        if (!$exists) {
            DB::table('users')->insert([
                'name'       => 'Guru',
                'username'   => 'guru',
                'password'   => Hash::make('guru123'),
                'role'       => 'guru',
                'group_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
