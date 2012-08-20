<?php
/**
|---------------------------------------------------------------
| 控制器基类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-08-14
|---------------------------------------------------------------
*/
Class CBasic{
	public $session;
	public function __construct() {
		$this->session = new CSession();
	}

}
?>