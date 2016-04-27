<?php

use App\User;
use App\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            "name"      =>  "Admin",
            "role"      =>  "admin"
        ]);

        $adminAccount = Account::create([
            "user_id"   =>  $admin->id,
            "type"      =>  "local",
            "email"     =>  "admin@example.com",
            "password"  =>  Hash::make("123123")
        ]);
        
        $users = [
            ["name" => "Elon", "surname" => "Musk"],
            ["name" => "Casey","surname" => "Neistat"],
            ["name" => "Conor","surname" => "McGregor"],
        ];

        foreach ($users as $index => $user) {
            $user = User::create([
                "name"      =>  $user["name"],
                "surname"   =>  $user["surname"]
            ]);
            Account::create([
                "user_id"   =>  $user->id,
                "type"      =>  "local",
                "email"     =>  "user".($index+1)."@example.com",
                "password"  =>  Hash::make("123123")
            ]);
        }

        for($i=0; $i<200; $i++){
            \App\LoginData::create(array(
                'account_id' => 1,
                'created_at' => date("Y-m-d H:i:s",rand(1460246400,1461660186))
            ));
        }
    }
}
