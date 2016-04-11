<?php

namespace App\Jobs;

use App\Jobs\Job;
use Faker\Factory;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\User;
use App\Role;
use Hash;

class TestJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(5);
        $user_role = Role::where('name','user')->first();

        $faker = Factory::create();
        $user = new User;
        $user->name = $faker->firstName;
        $user->login = $faker->name;
        $user->email = $faker->email;
        $user->password = Hash::make('123456');
        $user->account_type_id = $user_role->id;
        $user->save();
    }
}
