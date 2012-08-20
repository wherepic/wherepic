<?php
 class index extends admin{
 	public function __construct(){
 		parent::__construct();
 	}
 	public function init(){
 		include $this->template('index');
 	}
 	public function sms(){
 		include $this->template('index');
 	}

 	public function getPurview(){
 		$controller = $_GET['power'];
 		$isFunc = false;
 		$power = $this->power[$controller];
 		if(isset($power['group'])){
 			$menu = $power['group'];
 		}elseif(isset($power['func'])){
 			$isFunc = true;
 			$menu = $power['func'];
 		}
 		include $this->template('side_menu');
 	}
 }
?>