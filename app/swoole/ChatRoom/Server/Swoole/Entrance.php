<?php
namespace Swoole;
use function str_replace;

/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/13
 * Time: 下午1:26
 */
class Entrance
{
    private static $rootPath;
    private static $appPath='app';
    private static $configPath;
    private static $classPath=[];

    public static function getRootPath()
    {
        return self::$rootPath;
    }
    public static function autoLoader($class)
    {
        if (isset(self::$classPath[$class]))
        {
            require self::$classPath[$class];
            return;
        }
        $baseClassPath=str_replace('\\',DS,$class).'.php';
        $libs=[
            self::$rootPath.DS.self::$appPath,
            self::$rootPath
        ];
    }
}