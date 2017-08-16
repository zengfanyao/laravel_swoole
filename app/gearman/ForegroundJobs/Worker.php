<?php
/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/16
 * Time: 下午6:35
 */
$worker=new GearmanWorker();
$worker->addServer("10.8.7.184",4730);


$worker->addFunction("reverse","reverse_fn");

while (1)
{
    print "Waiting for job... \n";
    $ret=$worker->work();
    if ($worker->returnCode()!=GEARMAN_SUCCESS)
    {
        break;
    }
}
function reverse_fn(GearmanJob $job)
{
    $workload=$job->workload();
    echo "Received Job: ".$job->handle(). "\n";
    echo "Workload:$workload\n";
    $result=strrev($workload);
    echo "Result:$result\n";
    return $result;

}