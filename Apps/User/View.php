<?php
Class View extends User{

	public function __construct() {
		parent::__construct();
	}	    
	public function init(){
		var_dump($_REQUEST);
		//echo "USER.Index.view <Br/>";
		include T("view");
	}  
	public function asdasd(){
		var_dump($_REQUEST);
		//echo "USER.Index.view <Br/>";
		include T("view");
	}
}

?>