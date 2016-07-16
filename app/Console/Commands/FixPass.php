<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class FixPass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
    //    $users = User::where('register_from','<>','C')->get();
        $users = User::whereNull('register_from')->get();
      //  $users = User::get();

        $this->info(' Start. ' . "\n");
        echo count($users) . '\n';
        foreach($users as $user){

          $this->info($user->name . "\n");
          $user->password = bcrypt($user->password);
          $user->save();
        }
    }
}
