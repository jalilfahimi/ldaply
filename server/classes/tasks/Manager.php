<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace Core\ScheduledTask;

use Exception;
use CORE;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\ScheduledTask;
use App\Models\ScheduledTasksRun;


/**
 * 
 * 
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 */
final class Manager
{
    /**
     *
     * @var int
     */
    private $run = 0;

    /**
     *
     * @var int
     */
    private $start = 0;

    /**
     *
     * @var int
     */
    private $finish = 0;

    /**
     * 
     *  
     * @return void
     */
    public function updateTasks(): void
    {
        $this->load();

        $tasks = array();
        $classes = get_declared_classes();
        $hasChanges = false;

        $registered = ScheduledTask::orderBy('id')->get();

        foreach ($classes as $class) {
            if (is_subclass_of($class, BaseTask::class)) {
                array_push($tasks, $class);
            }
        }

        if ($registered->isEmpty()) {
            if (empty($tasks)) {
                CORE::logcli('Nothing to update. Exitting');
                return;
            }

            $hasChanges = true;
            foreach ($tasks as $task) {
                $model = new ScheduledTask();
                $model->classname = $task;
                $model->description = $task::description();
                $model->lastrun = 0;
                $model->interval = $task::interval();
                $model->enabled = $task::enabled();

                $model->save();

                CORE::logcli("Registered new task: $task.");
            }
        } else {
            $registeredTasksClasses = array();
            $registered = $registered;

            foreach ($registered as $registeredTask) {
                array_push($registeredTasksClasses, $registeredTask->classname);
                if (!in_array($registeredTask->classname, $tasks)) {
                    $hasChanges = true;
                    DB::table('scheduled_tasks')->delete($registeredTask->id);
                    CORE::logcli("Removed task: $registeredTask->classname.");
                }
            }

            foreach ($tasks as $task) {
                if (!in_array($task, $registeredTasksClasses)) {
                    $hasChanges = true;
                    $model = new ScheduledTask();
                    $model->classname = $task;
                    $model->description = $task::description();
                    $model->lastrun = 0;
                    $model->interval = $task::interval();
                    $model->enabled = $task::enabled();

                    $model->save();

                    CORE::logcli("Registered new task: $task.");
                }
            }
        }

        if (!$hasChanges) {
            CORE::logcli('Nothing to update. Exitting');
        }
    }

    /**
     * Run all tasks or the given one.
     * If $task is not specified, this functions loads all registered tasks.
     * Only those who have reached their configured intervals are executed.
     * Pass the classname of an specific task (Can be used for testing, and is discouraged for production sites).
     *  
     * @param string $task
     * @return void
     */
    public function runTasks($task = ''): void
    {
        $this->load();

        $tasks = array();

        if (empty($task)) {
            $all = ScheduledTask::orderBy('id')->where('enabled', 1)->get();
            foreach ($all as $task) {
                if (intval($task->lastrun) === 0 || time() - intval($task->lastrun) > intval($task->interval)) {
                    array_push($tasks, $task);
                }
            }
        } else {
            $tasks = ScheduledTask::where('classname', '=', $task)->get();
        }

        if (empty($tasks)) {
            CORE::logcli('No tasks to run. Exiting.');
            return;
        }

        if ($this->before()) {
            CORE::logcli('Started executing scheduled tasks.');

            foreach ($tasks as $task) {
                $instance = new $task->classname($this->run, $task->id);
                $instance->execute();
            }
            
            $this->after();

            CORE::logcli('Finished executing scheduled tasks.');
            CORE::logcli('Execution time in seconds: ' . intval($this->finish - $this->start));
        }
    }

    /**
     * Does all the essential operations before running tasks.
     * 
     *  
     * @return bool
     */
    private function before(): bool
    {
        try {
            if (CORE::isInMaintenanceMode()) {
                $msg = 'Site is in maintenance mode. Exiting.';
                error_log($msg);
                return false;
            }
            if (CORE::cronIsRunning()) {
                $msg = 'Cron is running right now. Try again a few moments later. Exiting.';
                error_log($msg);
                return false;
            }

            $this->start = time();

            $model = new ScheduledTasksRun();
            $model->started_at = $this->start;
            $model->finished_at = 0;
            $model->save();

            $this->run = $model->id;

            CORE::startCron();
            self::checkAndMakeLogDirectory();

            return true;
        } catch (Exception $e) {
            CORE::logcli($e);
            return false;
        }
    }

    /**
     * Does all the essential operations after running tasks.
     * 
     *  
     * @return bool
     */
    private function after(): bool
    {
        try {
            $this->finish = time();

            $model = ScheduledTasksRun::find($this->run);
            $model->finished_at = $this->finish;
            $model->save();

            CORE::stopCron();

            return true;
        } catch (Exception $e) {
            CORE::logcli($e);
            return false;
        }
    }

    /**
     *
     *  
     * @return void
     */
    private function load(): void
    {
        require_once('BaseTask.php');

        $dir = dirname(__DIR__, 1) . "/tasks";

        $core = glob($dir . '/core/*.php');
        $custom = glob($dir . '/custom/*.php');

        foreach ($core as $file) {
            require_once($file);
        }

        foreach ($custom as $file) {
            require_once($file);
        }
    }

    /**
     * 
     *  
     * @return string
     */
    public static function getLogDir(): string
    {
        return storage_path('logs/tasks/');
    }

    /**
     * 
     * 
     * @param string $classname
     * @param bool $isCore 
     * @return void
     */
    public static function makeTask(string $classname, bool $isCore = false): void
    {
        require_once('BaseTask.php');

        $dir = dirname(__DIR__, 1) . "/tasks";

        $core = glob($dir . '/core/*.php');
        $custom = glob($dir . '/custom/*.php');

        $files = array();

        foreach ($core as $file) {
            require_once($file);
            array_push($files, strtolower(substr(strrchr($file, "/"), 1)));
        }

        foreach ($custom as $file) {
            require_once($file);
            array_push($files, strtolower(substr(strrchr($file, "/"), 1)));
        }

        $filename = ucfirst($classname) . '.php';
        if (in_array(strtolower($filename), $files)) {
            CORE::logcli("File name $filename is already taken. Exiting.");
            return;
        }

        $classes = get_declared_classes();
        $tasks = array();

        foreach ($classes as $class) {
            if (is_subclass_of($class, BaseTask::class)) {
                array_push($tasks, strtolower(substr(strrchr($class, "\\"), 1)));
            }
        }

        if (in_array(strtolower($classname), $tasks)) {
            CORE::logcli("Class name $classname is already taken. Exiting.");
            return;
        }

        $content = str_replace('ExampleTask', ucfirst($classname), self::template());

        if ($isCore === true) {
            $path = dirname(__DIR__, 1) . "/tasks/core/$filename";
        } else {
            $path = dirname(__DIR__, 1) . "/tasks/custom/$filename";
        }

        File::put($path, $content);

        CORE::logcli("Created task $classname located in $path.");
    }

    /**
     * Checks if the log directory exists and creates it if not.
     *  
     * @return void
     */
    private static function checkAndMakeLogDirectory(): void
    {
        if (Storage::exists(BaseTask::getLogDir())) {
            Storage::build(['driver' => 'local', 'root' =>  BaseTask::getLogDir()]);
        }
    }

    /**
     * Return the string value for an example task classs.
     *  
     * @return string
     */
    private static function template(): string
    {
        return file_get_contents(dirname(__DIR__, 1) . "/tasks/Template.php");
    }
}
