<?php

/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/14
 * Time: 下午8:20
 */
require 'Task.php';
require 'SystemCall.php';
class Scheduler
{
    protected $maxTaskId = 0;
    protected $taskMap = [];
    protected $taskQueue;

    public function __construct()
    {
        $this->taskQueue = new SplQueue();
    }

    public function newTask(Generator $coroutine)
    {
        $tid = ++$this->maxTaskId;
        $task = new Task($tid, $coroutine);
        $this->taskMap[$tid] = $task;
        $this->schedule($task);
        return $tid;
    }

    public function schedule(Task $task)
    {
        $this->taskQueue->enqueue($task);
    }

    public function run()
    {
        while (!$this->taskQueue->isEmpty()) {
            $task = $this->taskQueue->dequeue();
            $revet = $task->run();
            if ($revet instanceof SystemCall) {
                $revet($task, $this);
                continue;
            }
            if ($task->isFinished()) {
                unset($this->taskMap[$task->getTaskId()]);
            } else {
                $this->schedule($task);
            }
        }
    }
}

function getTaskId()
{
    return new SystemCall(function(Task $task,Scheduler $scheduler){
        $task->setSendValue($task->getTaskId());
        $scheduler->schedule($task);
    });
}

function task1($max)
{
    $tid=(yield getTaskId());
    for($i=1;$i<=$max;++$i)
    {
        echo "This is task $tid iteration $i .\n";
        yield;
    }
}
$schedule=new Scheduler();
$schedule->newTask(task1(10));
$schedule->newTask(task1(5));