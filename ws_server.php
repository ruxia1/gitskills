<?php

/**
 * 
 */
class Ws
{
	CONST HOST = '0.0.0.0';
	CONST PORT = 9501;

	public $ws = null;
	public function __construct(){
		$this->ws = new swoole_websocket_server('0.0.0.0',9501);
		$this->ws->set([
			'worker_num'=>2,
			'task_worker_num'=>2,
			// 'enable_static_handler'=>true,
			// 'document_root'=>'/home/wwwroot/test01/data',
		]);

		$this->ws->on('open',[$this,'onOpen']);
		$this->ws->on('message',[$this,'onMessage']);
		$this->ws->on('task',[$this,'onTask']);
		$this->ws->on('finish',[$this,'onFinish']);
		$this->ws->on('close',[$this,'onClose']);

		$this->ws->start();
	}

	/**
	 * 监听ws连接事件
	 * @param  [type] $ws      [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function onOpen($ws,$request){
		var_dump($request->fd);

	}

	/**
	 * 监听ws消息事件
	 * @param  [type] $ws    [description]
	 * @param  [type] $frame [description]
	 * @return [type]        [description]
	 */
	public function onMessage($ws,$frame){
		echo "ser-push-message:{$frame->data}\n";
		$data = [
			'task'=>1,
			'fd'=>$frame->fd,
		];
		$ws->task($data);
		print_r('begin push');
		$ws->push($frame->fd,'server-push:'.date('Y-m-d H:i:s'));
	}

	public function onTask($serv,$taskId,$workerId,$data){
		print_r($data);
		sleep(10);
		return 'on task finish';
	}

	public function onFinish($serv,$taskId,$data){
		echo "taskId:{$taskId}\n";
		echo "finish-data-success:{$data}\n";
	}

	public function onClose($ws,$fd){
		echo "cliented:{$fd}\n";
	}
}

$obj = new Ws();