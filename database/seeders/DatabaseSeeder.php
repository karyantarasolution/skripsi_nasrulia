<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
  public function run(): void
  
{
    $this->call([
        AturanChatbotSeeder::class,
    ]);
    \App\Models\User::create([
        'name' => 'Admin NJK',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('password'),
        'peran' => 'admin',
    ]);

    \App\Models\User::create([
        'name' => 'Kasir NJK',
        'email' => 'kasir@gmail.com',
        'password' => bcrypt('password'),
        'peran' => 'kasir',
    ]);

    \App\Models\User::create([
        'name' => 'Pelanggan',
        'email' => 'user@gmail.com',
        'password' => bcrypt('password'),
        'peran' => 'pelanggan',
    ]);
    }
}
