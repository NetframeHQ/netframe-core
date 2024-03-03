<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        \DB::table('admins')->insert([
            [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@netframe.co',
                'password' => bcrypt('adminnetframe'),
                'created_at' => Date('Y-m-d H:i:s'),
                'updated_at' => Date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
