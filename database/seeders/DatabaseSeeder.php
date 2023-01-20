<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ProductSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
       $this->call([ProductSeeder::class, UserSeeder::class]);


    }
}
