<?php
/*
|---------------------------------------------------------------
| 模板admin下的控制器父类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

 class admin extends core{
 	public $power,$menu;
 	public function __construct(){
 		parent::__construct();
 		$this->getPower();
 		$this->getMenu();
 	}

 	public function getPower(){
 		$power = main::getConfig('purview');
 		$this->power = $power['nav'];
 		return $this->power;
 	}

 	public function getMenu(){
 		$controller = $_GET['controller'];
 		$isFunc = false;
 		$power = $this->power[$controller];
		$menu = $power['list'];
 		$this->menu = $menu;
 	}
}

?>