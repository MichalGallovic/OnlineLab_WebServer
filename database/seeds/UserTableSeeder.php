<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::where('name','admin')->first();

        $user = new User;
        $user->name = "Admin";
        $user->login = "admin";
        $user->email = "admin@example.com";
        $user->password = Hash::make('123456');

        $user->role()->associate($admin);
        $user->save();

    }
}
