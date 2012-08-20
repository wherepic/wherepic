<?php
/*
|---------------------------------------------------------------
| 程序入口
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
| http://www.domain.com/index.php?model=admin&controller=system&method=basic&id=1&id2=1
| htaccess 开启后(简化URL地址),具体设置请参考htaccess文件
| http://www.domain.com/admin-system/basic?id=1&id2=1
| model : 模块
| controller : 控制器
| method : 控制器下的方法
|---------------------------------------------------------------
*/

define("IN_JAING", true);
define("JAING_PATH",str_replace('\\','/',dirname(__FILE__)).'/');
define('JAING_VERSION', '1.0');
define('JAING_RELEASE', '20120601');
date_default_timezone_set('PRC');  //设置时区

/* for rewrite or iis rewrite */
if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
} else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
}
/* end */

/*常量*/
define('JAING_CLASS_PATH',JAING_PATH.'sourcs/class/');  //公共类库
define('JAING_FUNC_PATH',JAING_PATH.'sourcs/function/'); //公共函数库
define('JAING_CONFIG_PATH',JAING_PATH.'sourcs/configs/'); //公共配置文件夹
define('JAING_LANG_PATH',JAING_PATH.'sourcs/language/'); //公共配置文件夹

include_once(JAING_FUNC_PATH.'core.func.php');

/*调用系统控制器*/
$app = new main();
$app->runProgram();
?>