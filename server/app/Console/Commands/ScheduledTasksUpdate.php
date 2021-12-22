<?php

namespace App\Console\Commands;

use Exception;
use CORE;
use Core\ScheduledTask\Manager;
use Illuminate\Console\Command;

class ScheduledTasksUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduledtasks:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CLI command to manually update scheduled tasks.';

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
        try {
            require_once(dirname(__DIR__, 3) . "/classes/tasks/Manager.php");
            $manager = new Manager();
            $manager->updateTasks();
            return Command::SUCCESS;
        } catch (Exception $e) {
            CORE::logcli($e);
            return Command::FAILURE;
        }
    }
}
