<?php

namespace App\Console\Commands;

use Exception;
use CORE;
use Core\ScheduledTask\Manager;
use Illuminate\Console\Command;

class ScheduledTasksMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduledtasks:make {name} {--core}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =
    'CLI command to make a new scheduled task. Example: php artisan scheduledtasks:make DoSomethingTask. Use --core to make a core task.';

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

            $name = $this->argument('name');
            $core = $this->option('core');

            Manager::makeTask($name, $core);

            return Command::SUCCESS;
        } catch (Exception $e) {
            CORE::logcli($e);
            return Command::FAILURE;
        }
    }
}
