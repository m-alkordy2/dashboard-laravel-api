<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* $table->string('');
            $table->string('last_name');
            $table->string('user_name'); */
        DB::table('users')->insert([
        'first_name'=>"mohammed" ,
        'last_name'=>"alkordy" ,
        'user_name'=>"mohammed_alkordy" ,
        'email'=>"admin@gmail.com" ,
        'password'=>Hash::make('123123123')]);
    }
}
