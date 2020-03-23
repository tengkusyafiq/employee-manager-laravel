<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::truncate(); //to avoid duplicates if run more than one.
        // get the roles type in our roles table
        $bossRole = Role::where('name', 'boss')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $employeeRole = Role::where('name', 'employee')->first();

        // create users and later we will attach the roles
        $boss = User::create([
            'name' => 'phillip',
            'email' => 'phillip@vimigo.my',
            'password' => bcrypt('password'),
        ]);
        $manager = User::create([
            'name' => 'jess',
            'email' => 'jess@vimigo.my',
            'password' => bcrypt('password'),
        ]);
        $employee = User::create([
            'name' => 'syafiq',
            'email' => 'syafiq@vimigo.my',
            'password' => bcrypt('password'),
        ]);

        // attach users above to a role
        $boss->roles()->attach($bossRole);
        $manager->roles()->attach($managerRole);
        $employee->roles()->attach($employeeRole);
    }
}
