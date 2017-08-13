<?php

/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/13
 * Time: 下午3:28
 */
class DbServer
{
    private $_serv;
    private $_db_config;
    private $_wait_queue = []; //等待数组
    private $_free_task_table;
    private $_busy_task_table;
    private $_request_size = 0;

    public function __construct($config)
    {
        $this->_db_config=$config;
        $this->_serv=new \swoole_server("0.0.0.0",9905);
        $this->_serv->set([
            'worker_num'       => 1,
            'task_worker_num'  => 2,
            'task_max_request' => 0,
            'max_request'      => 0,
            'log_file'         => '/tmp/swoole_dbpool.log',
            'dispatch_mode'    => 2
        ]);
        $this->_serv->on('Start', array($this, 'onStart'));
        $this->_serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->_serv->on('Receive', array($this, 'onReceive'));
        // Task 回调的2个必须函数
        $this->_serv->on('Task', array($this, 'onTask'));
        $this->_serv->on('Finish', array($this, 'onFinish'));
    }

    public function run()
    {
        $this->_wait_queue=[];
        $this->_free_task_table=new \swoole_table(1024);
        $this->_free_task_table->column('free_id', swoole_table::TYPE_STRING, 1000);
        $this->_free_task_table->create();
        $this->_free_task_table->set('list', array('free_id' => json_encode(range(0, 1))));
        $this->_busy_task_table = new swoole_table(1024);
        $this->_busy_task_table->column('busy_id', swoole_table::TYPE_STRING, 1000);
        $this->_busy_task_table->create();
        $this->_busy_task_table->set('list', array('busy_id' => json_encode(array())));
        $this->_request_size = 0;
        $this->_serv->start();

    }

    public function onStart($serv)
    {
        \swoole_set_process_name("php5 master {$serv->master_pid}");
    }
//    public function onWorkerStart($serv,$worker_id)
//    {
//        if ($worker_id>=)
//    }
}