<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use \Exception;

class RemoveUnVerifiedUsersFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-un-verified-users-from-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'With this command you can remove the un verified users from the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            User::where(['email_verified_at'=>null])->delete();
            $this->info('Unverified users are successfully deleted!');
        } catch(Exception $e){
            $this->error($e->getMessage());
        }
    }
}
