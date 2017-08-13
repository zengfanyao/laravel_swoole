<?php
/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/13
 * Time: 下午1:25
 */

use Swoole\Entrance;
$rootPath=__DIR__;
require $rootPath.DIRECTORY_SEPARATOR.'Swoole'.DIRECTORY_SEPARATOR.'Entrance.php';
Entrance::run($rootPath);