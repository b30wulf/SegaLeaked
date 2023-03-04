<?php

namespace SegaCore\Core\task;

use pocketmine\scheduler\Task;
use SegaCore\Core\Main;
use pocketmine\Server;

class ToastBroadcasterTask extends Task {
    
    	private Main $main;
    
    	private $i;
    
    	public function __construct(Main $main){
        	$this->main = $main;
		    $this->i = 0;
    	}

    	public function onRun() : void {
        	$title = $this->main->getConfig()->get("title");
        	$messages = $this->main->getConfig()->getAll()["broadcast"]["message"];
    		back:
    		if($this->i < count($messages)){
    	    		
                Server::getInstance()->broadcastMessage($title . " " . $messages[$this->i]);
    	    		$this->i++;
    		}else{
		    	$this->i = 0;
		    	goto back;
		}
    	}
}