<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 *
 * This class is responsible for seeding the database with initial data.
 * It extends the Laravel's Seeder class.
 *
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method is responsible for creating the initial data in the database.
     * It uses the User factory to create 5 users.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(5)
            ->create();
    }
}