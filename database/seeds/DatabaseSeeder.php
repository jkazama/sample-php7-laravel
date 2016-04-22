<?php

use App\Models\DataFixtures;
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
        // todo: 環境別の考慮
        $fixture = new DataFixtures();
        $fixture->initialize();
    }
}
