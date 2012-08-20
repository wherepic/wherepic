<?php

/*
|---------------------------------------------------------------
| 核心处理方法
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

if(!defined('IN_JAING')) {header('HTTP/1.1 404 Not Found');}

/**
* 自动加载类
* 
* @param mixed $funcpre
*/
function __autoload($class_name) {
	$file_path = JAING_CLASS_PATH . $class_name.'.class.php';
	if ( file_exists( $file_path ) ) { 
		return require_once( $file_path );
	}else{
		$file_path = JAING_PATH.'controllers/'.ROUTE_M."/class/". $class_name.'.class.php';	
		if ( file_exists( $file_path ) ) {
			return require_once( $file_path );	
		}
	}

	
	return false;
}

/**
* 载入函数文件
* 
* @param mixed $funcpre
*/
function import($funcpre,$path = false) {
	$file_path = JAING_FUNC_PATH;
	if($path){
		$file_path .= 'controllers/'.ROUTE_M."/function/";
	}
	$file_path .= $funcpre.'.func.php'; 
	if (file_exists($file_path) ) {
		require_once( $file_path );
	}else{
		exit("No found function[$funcpre] !");
	}
}

/**
* 返回字符串在另一个字符串中第一次出现的位置
* 
* @param mixed $haystack
* @param mixed $needle
* @return mixed
*/
function strexists($haystack, $needle) {
	return !(strpos($haystack, $needle) === FALSE);
}

function tplcompare($filename,$tpldir,$tplfile,$cache_file,$timestamp){
	$template = new template();
	$template -> tplcompare($filename,$tpldir,$tplfile,$cache_file,$timestamp);
}

/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function makeDir($folder){
	$reval = false;
	if (!file_exists($folder)){
		/* 如果目录不存在则尝试创建该目录 */ 
		@umask(0);
		/* 将目录路径拆分成数组 */
		preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
		/* 如果第一个字符为/则当作物理路径处理 */
		$base = ($atmp[0][0] == '/') ? '/' : '';
		/* 遍历包含路径信息的数组 */
		foreach ($atmp[1] AS $val){
			if ('' != $val){
				$base .= $val;
				if ('..' == $val || '.' == $val){
					/* 如果目录为.或者..则直接补/继续下一个循环 */
					$base .= '/';
					continue;
				}
			}
		else{
			continue;
		}
		$base .= '/';
		if (!file_exists($base)){
			/* 尝试创建目录，如果创建失败则继续循环 */
			if (@mkdir($base, 0777))
			{
				@chmod($base, 0777);
				$reval = true;
			}
		}
	}
}
else{
	/* 路径已经存在。返回该路径是不是一个目录 */
	$reval = is_dir($folder);
}
	clearstatcache(); 
	return $reval;
}


?>