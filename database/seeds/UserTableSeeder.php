<?php

use App\User;
use App\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\LoginData;

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
/*
        for($i=0; $i<200; $i++){
            $data = new LoginData();

            $data->account()->associate($adminAccount);
            $data->created_at = date("Y-m-d H:i:s",rand(1460246400,1461660186));
            $data->setLocationAttribute($this->float_rand(45,50,6).','.$this->float_rand(5,25,6));
            $data->save();
            //50, 5
            //50, 25
            //45, 25
            //45, 5

        }
*/

        for($i=0; $i<120; $i++){
            $data = new LoginData();

            $data->account()->associate($adminAccount);
            $data->created_at = date("Y-m-d H:i:s",rand(1460246400,1461660186));
            $data->setLocationAttribute('48.1500,17.1167');
            $data->save();
            //50, 5
            //50, 25
            //45, 25
            //45, 5

        }

        for($i=0; $i<460; $i++){
            $data = new LoginData();

            $data->account()->associate($adminAccount);
            $data->created_at = date("Y-m-d H:i:s",rand(1460246400,1461660186));
            $data->setLocationAttribute('48.6667,19.5000');
            $data->save();
            //50, 5
            //50, 25
            //45, 25
            //45, 5

        }
    }

    private function float_rand($min, $max, $round=0){
        //validate input
        $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        if($round>0)
            $randomfloat = round($randomfloat,$round);
        return $randomfloat;
    }
}
