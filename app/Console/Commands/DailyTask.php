<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:daily_task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for Daily Task Project';

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
        return 0;
    }
}
