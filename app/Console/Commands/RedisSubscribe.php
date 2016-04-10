<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Chat\Entities\Message;
use Redis;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to Redis Channel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

     private function object_to_array($data)
     {
         if (is_array($data) || is_object($data))
         {
             $result = array();
             foreach ($data as $key => $value)
             {
                 $result[$key] = $this->object_to_array($value);
             }
             return $result;
         }
         return $data;
     }

    public function handle()
    {
        Redis::subscribe(['chatroom'], function($message) {
            echo $message;
            $object = json_decode($message);

            //var_dump($object);

            Message::create($this->object_to_array($object));
        });
    }
}
