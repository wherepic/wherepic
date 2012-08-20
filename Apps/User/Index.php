<?php
Class Index extends User{

	public function __construct() {
		parent::__construct();
	}	    
	public function init(){
		$user = $_GET['user'];
		include T("view");
	}
}

?>