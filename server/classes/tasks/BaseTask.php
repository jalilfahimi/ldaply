<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace Core\ScheduledTask;

use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Models\ScheduledTasksLog;


/**
 * 
 * 
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 */
abstract class BaseTask
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
     * @var string
     */
    private $logFileName = '';

    /**
     *
     * @var string
     */
    private $logFilePath = '';

    /**
     * Return the default description
     * 
     * @return string
     */
    public abstract static function description(): string;

    /**
     * Return the default interval in seconds
     * 
     * @return int
     */
    public abstract static function interval(): int;

    /**
     * Return the default enabled
     * 
     * @return bool
     */
    public abstract static function enabled(): bool;

    /**
     * Do the logic here.
     * 
     * @return void
     */
    public abstract function execute(): void;

    /**
     * 
     *  
     * @param int $run the id of current run.
     * @param int $task the id of current task.
     */
    public final function __construct(int $run, int $task)
    {
        $this->run = $run;
        $this->task = $task;

        $this->start = time();
        $this->finish = 0;

        $this->logFileName = $run . '_' . $task .  self::getLogExtension();
        $this->logFilePath = self::getLogDir() . $this->logFileName;

        if (Storage::exists($this->logFilePath)) {
            Storage::delete($this->logFilePath);
        }

        $disk = Storage::build(['driver' => 'local', 'root' =>  self::getLogDir()]);

        $disk->put($this->logFileName, '');

        $taskModel = \App\Models\ScheduledTask::find($task);
        $taskModel->lastrun = $this->start;
        $taskModel->save();

        $this->log('Started executing scheduled task: ' . $this->getName());
    }

    /**
     * Saves everything related to task and logs.
     * 
     */
    public final function __destruct()
    {
        $this->finish = time();

        $model = new ScheduledTasksLog();
        $model->task = $this->task;
        $model->run = $this->run;
        $model->path = $this->logFilePath;
        $model->started_at = $this->start;
        $model->finished_at = $this->finish;
        $model->save();

        $this->log('Finished executing scheduled task: ' . $this->getName());
        $this->log('Execution time in seconds: ' . intval($this->finish - $this->start));
    }

    /**
     * 
     *  
     * @return string
     */
    public final function getName(): string
    {
        return get_class($this);
    }

    /**
     * 
     *  
     * @return string
     */
    public final static function getLogDir(): string
    {
        return storage_path('logs/tasks/');
    }

    /**
     * 
     *  
     * @return string
     */
    public final static function getLogExtension(): string
    {
        return '.log';
    }

    /**
     * 
     *  
     * @param string $msg
     * @return void
     */
    protected final function log(string $msg): void
    {
        $logger = new Logger('tasks');
        $logger->pushHandler(new StreamHandler($this->logFilePath), Logger::DEBUG);
        $logger->debug($msg);
    }
}
