<?php
/*
|---------------------------------------------------------------
| 程序入口
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
| model : 模块
| controller : 控制器
| method : 控制器下的方法
|文件夹首字母为大写，文件夹、文件命名不能出来横杆（-）
|访问模块下的控制器：
|完整路径：http://www.domain.com/index.php?model=admin&controllers=index&method=view
|伪静态：http://www.domain.com/admin-index/view?
-----------------------------------------------------------------
|访问项目目录下的控制器
|完整路径：http://www.domain.com/index.php?controllers=index&method=view
|伪静态：http://www.domain.com/index-view?
|---------------------------------------------------------------
*/

//记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);

define("IN_CPHP", true);
define("__CROOT__",str_replace('\\','/',dirname(__FILE__)).'/');
define("__APPDEBUG__",true); //TRACE输出
define("__DOMAIN__",'http://'.$_SERVER['HTTP_HOST'].'/');
define("DOMAIN",'http://'.$_SERVER['HTTP_HOST'].'/');
define('C_VERSION', '1.0');
define('C_RELEASE', '20120601');
define('C_RELEASE', '20120601');
date_default_timezone_set('PRC');  //设置时区

/* for rewrite or iis rewrite */
if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
} else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
}
/* end */

/*目录*/
define('__CSOURCS__',__CROOT__.'Sourcs/');  //公共类库
define('__CCLASS__',__CSOURCS__.'Core/Class/');  //公共类库
define('__CFUNC__',__CSOURCS__.'Core/Function/'); //公共函数库
define('__CCONF__',__CSOURCS__.'Conf/'); //公共配置文件夹
define('__CLANG__',__CSOURCS__.'Language/'); //语言包目录
define('__CTPL__', __CROOT__.'Template/'); //模板目录
define('__CCACHE__', __CROOT__.'Cache/'); //缓存目录
define('__CPUBLIC__', __CROOT__.'Public/'); //公共文件夹
define('__CAPPS__', __CROOT__.'Apps/'); //模块
define('__CUPLOAD__', __CROOT__.'Attachments/'); //上传文件保存目录
define('__CPLUGIN__', __CROOT__.'Plugin/'); //插件目录
define('__CLOG__', __CCACHE__.'Logs/'); //插件目录

/*公共访问地址*/
define("PUBLIC_IMAGES",__DOMAIN__.'Public/Images/');
define("PUBLIC_FLASH",__DOMAIN__.'Public/Flash/');
define("PUBLIC_JS",__DOMAIN__.'Public/Script/');

/*系统信息*/
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

/*当前文件名*/
if(!defined('__CFILE__')) {
    if(IS_CGI) {
        //CGI/FASTCGI模式下
        $_temp  = explode('.php',$_SERVER['PHP_SELF']);
        define('__CFILE__',  rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
    }else {
        define('__CFILE__',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
    }
}

include_once(__CFUNC__.'Core.func.php');
//初始程序，解析路由
CApp::start();
//echo parse_name('mdjs Jsis ',1);

?>