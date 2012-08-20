<?php

/*
|---------------------------------------------------------------
| 通行证控制器
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class reg extends core{
	public function __construct(){
 		parent::__construct();
 	}

 	/*
	用户登录界面
 	*/
 	public function init(){
 		include $this->template("reg");
 	}
}
?>