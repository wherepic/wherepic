<?php
 class index extends core{
 	public function __construct(){
 		parent::__construct();
 	}
 	public function init(){
 		var_dump($_GET);
 		echo $_GET['u'];
 	}
 }
?>