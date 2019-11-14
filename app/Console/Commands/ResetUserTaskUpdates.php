<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetUserTaskUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-task-tracker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If user not update his task more then 1 week he will be back to previous week';

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
        //
    }
}
