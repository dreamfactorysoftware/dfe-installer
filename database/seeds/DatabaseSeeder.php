<?php

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
        $this->call(MountTTableSeeder::class);
        $this->call(EnvironmentTTableSeeder::class);
        $this->call(ServerTypeTTableSeeder::class);
    }
}
