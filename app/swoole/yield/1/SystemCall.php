<?php

/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/14
 * Time: 下午8:52
 */
require "Task.php";
require "Scheduler.php";
class SystemCall
{
    protected $callback;
    public function __construct(callable $callback)
    {
        $this->callback=$callback;
    }
    public function __invoke(Task $task,Scheduler $sche)
    {
        // TODO: Implement __invoke() method.
        $callback=$this->callback;
        return $callback($task,$sche);
    }
}