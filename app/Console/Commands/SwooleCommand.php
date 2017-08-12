<?php

namespace App\Console\Commands;

use function foo\func;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class SwooleCommand extends Command
{

    public $serv;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole {action} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $option = $this->argument('action');


        switch ($option)
        {
            case "start":
                $this->info('swoole observer started');
               // $this->start();
                $this->start1();
                break;
            case "stop":
                $this->info('stoped');
                $this->stop();
                break;
            case "restart":
                $this->info("restarted");
                $this->restart();
                break;
        }
    }

    public function start1()
    {
        $server=new \swoole_server("127.0.0.1",9501);

        $process = new \swoole_process(function($process) use ($server) {
            while (true) {
                $msg = $process->read();
                foreach($server->connections as $conn) {
                    $server->send($conn, $msg);
                }
            }
        });

        $server->addProcess($process);
        $server->on('receive',function ($serv,$fd,$from_id,$data)use ($process){
            $process->write($data);
        });
        $server->start();
    }

    private function start()
    {

        $this->serv=new \swoole_server("127.0.0.1",9501,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);
        $this->serv->set([
            'worker_num'=>1,
          //  'daemonize'=>false,
          //  'max_request'=>10000,
          //  'dispatch_mode'=>2,
           // 'debug_mode'=>1
        ]);


        $this->serv->on('connect',function ($serv,$fd){});
        $this->serv->on('receive',function ($serv,$fd,$from_id,$data){});
        $this->serv->on('close',function ($serv,$fd){});

        $this->serv->BaseProcess="I an process";

        $this->serv->on('start',function(\swoole_server $serv){
           \Log::info('On master start');
           \Log::info("server->BaseProcess = ". $this->serv->BaseProcess);
            // 修改交互进程中写入的数据
            $this->serv->BaseProcess = "I'm changed by master.";
            // 在Master进程中写入一些数据，以传递给Manager进程。
            $this->serv->MasterToManager = "Hello manager, I'm master.";

        });


        $this->serv->on('ManagerStart',function(\swoole_server $serv){
            \Log::info("On manager start");
            // 打印，然后修改交互进程中写入的数据
            \Log::info("server->BaseProcess = ".$this->serv->BaseProcess);
            $this->serv->BaseProcess="I am changed by manager";
           // \Log::info("server->masterToManager=".!empty($this->serv->MasterToManager) ? $this->serv->MasterToManager:"");
            // 打印，然后修改在Master进程中写入的数据
            $this->serv->MasterToManager=" this value has changed in manager";
            // 写入传递给Worker进程的数据
            $this->serv->ManagerToWorker="Hello worker I am manager";
        });
        $this->serv->on("ManagerStop",function (\swoole_server $serv){
            \Log::info("On manager stop");
        });

        $this->serv->on('WorkerStart',function(\swoole_server $serv,$worker_id){
            \Log::info("Worker start");
            \Log::info('server->BaseProcess='.$this->serv->BaseProcess);
            // 打印，并修改Master写入给Manager的数据
            //\Log::info("server->MasterToManager= ".!empty($serv->MasterToManager) ? $serv->MasterToManager : "");
            $this->serv->MasterToManager=" this value has changed in worker";
            //\Log::info("server->ManagerToWorker=".!empty($this->serv->ManagerToWorker) ? $this->serv->ManagerToWorker :"");
            // 打印，并修改Manager传递给Worker进程的数据
            $this->serv->ManagerToWorker = "This value is changed in worker.";

        });
        $this->serv->on('WorkerStop',function(\swoole_server $serv,$worker_id){
            \Log::info('Worker stop');
            \Log::info("server->ManagerToWorker=".$this->serv->ManagerToWorker);
            \Log::info("server->MasterToManager= ".$this->serv->MasterToManager);
            \Log::info('server->BaseProcess='.$this->serv->BaseProcess);
        });
        $this->serv->on('WorkerError',function(\swoole_server $serv,$worker_id,$worker_pid){
            \Log::info('Worker Error');
        });

        $this->serv->on('ManagerStop',function(\swoole_server $server){
            \Log::info("Manager stop");
            \Log::info("server->ManagerToWorker=".$this->serv->ManagerToWorker);
            \Log::info("server->MasterToManager= ".$this->serv->MasterToManager);
            \Log::info('server->BaseProcess='.$this->serv->BaseProcess);
        });

        $this->serv->on('shutdown',function (\swoole_server $serv){
            \Log::info("On master shutdown");
           // \Log::info("server->ManagerToWorker=".$this->serv->ManagerToWorker);
          //  \Log::info("server->MasterToManager= ".$this->serv->MasterToManager);
            \Log::info('server->BaseProcess='.$this->serv->BaseProcess);
        });
//        $handler=\App::make('App\Handlers\SwooleHandler');
//        $this->serv->on('Start',array($handler,'onStart'));
//        $this->serv->on('Connect',array($handler,'onConnect'));
//        $this->serv->on('Receive',array($handler,'onReceive'));
//        $this->serv->on('Close',array($handler,'onClose'));
        $this->serv->start();
    }

    private function stop()
    {
        $file=config('swoole.pidfile');
        if (!file_exists($file))
        {
            throw new \Exception("file not exists");
        }
        $string=file_get_contents($file);
        $string=json_decode($string,true);

        if (!empty($string))
        {
           try{
               if (!function_exists('shell_exec'))
               {
                   throw new \Exception("no support exec");
               }
               foreach ($string as $v)
               {
                   $pid='kill -9 '.$v;
                   shell_exec($pid);
               }
               unlink($file);
           }catch (\Exception $e)
           {
                $this->info('stop failed');
           }
           exit();
        }else
        {
            $this->error("no found pid");
        }
    }

    public function restart()
    {

    }

    protected function getArguments()
    {
        return [
            ['action',InputArgument::REQUIRED,'start|stop|restart']
        ];
        //return parent::getArguments(); // TODO: Change the autogenerated stub
    }


    public function fire()
    {

    }

}
