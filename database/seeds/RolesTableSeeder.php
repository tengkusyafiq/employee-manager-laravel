<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Role::truncate(); //to avoid duplicates if run more than one.
        Role::create(['name' => 'boss']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'employee']);
    }
}
