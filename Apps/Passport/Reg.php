<?php
define("IS_STYLE_PUBLIC",true);
Class Reg extends CBasic{

 	public function __construct(){
 		parent::__construct();
 	}	

 	public function init(){

 		if($_POST){
            json(array(
                'step' => R('domain'),
            ));
 		}

 		include T("reg");
 	}

     public function domain(){
         if($_POST){
            json(array(
                'step' => R('skin'),
            ));
         }         
         include T("reg_domain");
     }
 	public function skin(){
 		include T("reg_skin");
 	}

}
?>