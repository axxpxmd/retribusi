<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('templates')->insert([
            'logo' => 'logo.png',
            'logo_title' => 'logo_title.png',
            'logo_auth' => 'logo_auth.png'
        ]);
    }
}
