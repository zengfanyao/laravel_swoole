<?php

/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/14
 * Time: 下午8:14
 */
require 'Scheduler.php';
require 'SystemCall.php';
class Task
{
    protected $taskId;
    protected $coroutine;
    protected $sendValue=null;
    protected $beforeFirstYield=null;

    public function __construct($taskId,Generator $coroutine)
    {
        $this->taskId=$taskId;
        $this->coroutine=$coroutine;
    }
    public function getTaskId()
    {
        return new SystemCall(function(Task $task,Scheduler $scheduler){
            $task->setSendValue($task->getTaskId());
            $scheduler->schedule($task);
        });

    }
    public function setSendValue($sendValue)
    {
        $this->sendValue=$sendValue;
    }
    public function run()
    {
        if ($this->beforeFirstYield)
        {
            $this->beforeFirstYield=false;
            return $this->coroutine=current();
        }else
        {
            $retval=$this->coroutine->send($this->sendValue);
            $this->sendValue=null;
            return $retval;
        }
    }
    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}