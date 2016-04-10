<?php

use Illuminate\Database\Seeder;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$admin = AccountType::where('name','admin')->first();
        //$user->role()->associate($admin);

        $user = new User;
        $user->name = "Matej";
        $user->surname = "Rábek";
        $user->role = "admin";

        //$user->password = Hash::make('tok3rAt9');
        //$user->login = "admin";
        //$user->email = "admin@example.com";
        $user->language_code = 'sk';


        $user->save();

    }
}
