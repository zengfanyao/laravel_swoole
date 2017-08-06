<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SwooleProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole_process';

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
                $this->info('wsprocess observer started');
                $this->start();
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
    public function start()
    {

    }
}
