<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_details')->insert([
            'admin_id' => 1,
            'full_name' => 'Asip Hamdi',
            'email' => 'asiphamdi13@gmail.com',
            'phone' => '083897229273',
            'photo' => 'default.png'
        ]);
    }
}
