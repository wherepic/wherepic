<?php

/**
|---------------------------------------------------------------
| 完成URL解析路由
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class CApp{

	static public function Start(){
		self::init();
        $ClassFile = self::checkControl();
		require($ClassFile);
		$ClassName = __PC__;
		if(class_exists($ClassName)){
			$Class = new $ClassName;
		}else{
             CLog::write(str_replace('%s%',  strtolower((defined('__PM__') && __PM__ ? __PM__.'->':'').(__PC__)), L("ERR_CLASS_NOT_FOUND")));
        }
		if (method_exists($Class, __PA__)) {
			call_user_func(array($Class, __PA__));
		}else{
            CLog::write(str_replace('%s%',  strtolower((defined('__PM__') && __PM__ ?( __PM__.'->'.__PC__.'->'.__PA__):(__PC__.'->'.__PA__))), L("ERR_ACTION_NOT_FOUND"))); 
        }
	}

	static private function init(){
		if(!get_magic_quotes_gpc()) {
			$_POST = self::daddslashes($_POST);
			$_GET = self::daddslashes($_GET);
			$_REQUEST = self::daddslashes($_REQUEST);
			$_COOKIE = self::daddslashes($_COOKIE);
			$_FILES = self::daddslashes($_FILES);
		}
		C(include_once __CCONF__.'Common.conf.php');
		define('__PM__',self::getModule());		
		define('__PC__',self::getControl());		
		define('__PA__',self::getAction());	
       
        if(isset($_REQUEST['isAjax'])){
            define('__AJAXD__',true);
        }else{
            define('__AJAXD__',false);
        }        
        //if(isset($_REQUEST['isXml'])){define('__XMLD__',true);}	
	} 

    /**
     |----------------------------------------------------------
     | 获得模块名称
     |----------------------------------------------------------
     */
    static private function getModule($var = 'model') {
        $module = (!empty($_GET[$var])? $_GET[$var]:C('DEFAUL_C_MODULE'));
        unset($_GET[$var]);
        define('__MODULENAME__',strtolower($module));
        $module = ucfirst(parse_name(__MODULENAME__));
        return strip_tags($module);
    }

    /**
     |----------------------------------------------------------
     | 获得控制器名称
     |----------------------------------------------------------
     */
    static private function getControl($var = 'controller') {
        $control = (!empty($_GET[$var])? $_GET[$var]:C('DEFAUL_C_CONTROL'));
        unset($_GET[$var]);
        $control = ucfirst(parse_name($control));
        return strip_tags($control);
    }

    /**
     |----------------------------------------------------------
     | 获得操作名称
     |----------------------------------------------------------
     */
    static private function getAction($var = 'action') {
        $action  = (!empty($_GET[$var])?$_GET[$var]:C('DEFAUL_C_ACTION'));

        unset($_GET[$var]);
        return strip_tags($action);
    }
    
    /**
     |----------------------------------------------------------
     | 检查控制器是否存在 
     |----------------------------------------------------------
     */
    static private function checkControl(){
        $ClassFile = __CAPPS__;
        if(defined('__PM__') && __PM__) $ClassFile .= __PM__.'/';
        if(is_file($ClassFile.__PC__.'.php')){
            return $ClassFile.__PC__.'.php';
        }elseif(is_file($ClassFile.strtolower(__PC__).'.php')){
            return $ClassFile.strtolower(__PC__).'.php';
        }else{
            CLog::write(str_replace('%s%',  strtolower((defined('__PM__') && __PM__ ? __PM__.'->':'').(__PC__)), L("ERR_MODULE_NOT_FOUND")));    
        }      
    }

}

?>