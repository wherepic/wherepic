<?php

/*
|---------------------------------------------------------------
| SESSION处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class Session{
    public function __construct(){
        session_start();
    }
    
    public function Set($name, $v) 
    {
        $_SESSION[$name] = $v;
    }
    
    public function Get($name, $key = '', $once=false)
    {
        $v = null;
        if($key && isset($_SESSION[$key])){
            $v = $_SESSION[$key][$name];

        }elseif( isset($_SESSION[$name])){
            $v = $_SESSION[$name];
        }
        if ($once){ unset( $_SESSION[$name] );}
        return $v;
    } 
    public function removeAll($key = ''){
		if($key){
			unset($_SESSION[$key]);
		}else{
			session_unset();
			session_destroy();		
		}
    }   
    
}