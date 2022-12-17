<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class cronEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:email';

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
     * @return int
     */
    public function handle()
    {
       $user = User::all();
           foreach ($user as $all)
           {
             Mail::raw("This is automatically generated Minute Update", function($message) use ($all)
             {
                 $message->from('rohitatoms02@gmail.com');
                 $message->to($all->email)->subject('Minute Update');
             });
         }
         $this->info('Minute Update has been send successfully');
    }
}
