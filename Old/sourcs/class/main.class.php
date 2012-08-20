<?php

/*
|---------------------------------------------------------------
| 程序访问路由控制类,处理提交数据安全性
| 单一程序入口index.php
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Modified 2012-04-15
|---------------------------------------------------------------
| http://www.domain.com/index.php?model=admin&controller=system&method=basic&id=1&id2=1
| htaccess 开启后(简化URL地址),具体设置请参考htaccess文件
| http://www.domain.com/admin-system/basic?id=1&id2=1
| model : 模块
| controller : 控制器
| method : 控制器下的方法
|---------------------------------------------------------------
*/
/**
* 控制器选择
* Author CAO JIAYIN
* Mail caojiayin1984@gmail.com
* Last Date 2012-04-15
**/

class main{
	private $defaultController = array('controller' => 'index', 'method' => 'init');
	public function __construct(){
		if(!get_magic_quotes_gpc()) {
			$_POST = self::daddslashes($_POST);
			$_GET = self::daddslashes($_GET);
			$_REQUEST = self::daddslashes($_REQUEST);
			$_COOKIE = self::daddslashes($_COOKIE);
			$_FILES = self::daddslashes($_FILES);
		}		
		define('ROUTE_M',$this->get_model());
		define('ROUTE_C',$this->get_controller());
		define('ROUTE_F',$this->get_method());
        $lang = $_GET['lang'];
        if($lang){define("setLang",$lang);}	
	}

	public function runProgram(){
		$path = JAING_PATH . 'controllers/';
		if(ROUTE_M){
			$path .= ROUTE_M."/";
		}
		$file = $path.ROUTE_C.".php";
 
		if (file_exists($file)) {
			$classname = ROUTE_C;
			require($file);						

			if(!class_exists($classname)){
				self::ShowError('Module \''.$classname.'\' not found ');
			}
			
			$class = new $classname;

			if (method_exists($class, ROUTE_F)) {
				call_user_func(array($class, ROUTE_F));
			}else{ 
				self::ShowError(ROUTE_F.' Action does not exist.');
			}            
		}else{
			self::ShowError(ROUTE_C.' Page does not exist.');
		}		

	}
	public function get_model(){
		$model = isset($_GET['model']) && !empty($_GET['model']) ? $_GET['model'] : (isset($_POST['model']) && !empty($_POST['model']) ? $_POST['model'] : '');   

		return $model;
	}
	public function get_controller(){
		$controller = isset($_GET['controller']) && !empty($_GET['controller']) ? $_GET['controller'] : (isset($_POST['controller']) && !empty($_POST['controller']) ? $_POST['controller'] : ''); 
		if(empty($controller)){
			$controller = $this->defaultController['controller'];
		}        
		return $controller;
	}
	
	public function get_method(){
		$method = isset($_GET['method']) && !empty($_GET['method']) ? $_GET['method'] : (isset($_POST['method']) && !empty($_POST['method']) ? $_POST['method'] : ''); 
		if(empty($method)){
			$method = $this->defaultController['method'];
		}
		return $method;
	}
	/**
	* 加载配置文件
	* 
	* @param mixed $file
	*/
	public static function getConfig($file,$path = ''){
		if(empty($file)) return null;
		$config = '';
		$path = $path ? $path : JAING_CONFIG_PATH;
		$path .= $file.'.conf.php';
		if (file_exists($path)) {
			$config = include $path;
		}else{
			self::ShowError('Config does not exist.');
		}
		return $config;        
	}	
	public static function daddslashes($string, $force = 0) {
		$string=dhtmlspecialchars($string);
		!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
		if(!MAGIC_QUOTES_GPC || $force) {
			if(is_array($string)) {
				foreach($string as $key => $val) {
					$string[$key] = self::daddslashes($val, $force);
				}
			} else {
				$string = str_replace("'","&acute;",$string);
				$string = addslashes($string);
			}
		}
		return $string;
	}
	/**
	* 页面出错
	* 
	* @param mixed $msg
	*/
	public static function ShowError($msg){
		echo "<style>body,html{background:#fff}</style>";
		echo '<center style="padding-top:5%;"><span style="padding:20px;background:#fff;color:#666;font-family: Arial;border:10px solid #eee;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;">'.$msg.'<span></center>';
		exit;
	} 

	public static function debug($out){
		echo "<style>*{font-size:12px;background:#eee;color:#333}</style><pre>";
		print_r($out);
	} 

}
?>