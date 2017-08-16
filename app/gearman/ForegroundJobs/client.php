<?php
/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/16
 * Time: 下午6:33
 */

$client=new GearmanClient();
$client->addServer("10.8.7.184","4730");
echo "Sending job\n";

$result=$client->doNormal("reverse","Hello!");
if ($result)
{
    echo "Success:$result\n";
}
