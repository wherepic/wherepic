<?php

/**
|---------------------------------------------------------------
| 基础函数库
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

if(!defined('IN_CPHP')) {exit;}

//自动加载
function __autoload($class_name) {
    
    $ClassFile = __CCLASS__ . $class_name.'.class.php'; 
  
    if(file_exists($ClassFile)){
    	return require_once( $ClassFile );	
    }else{
    	$ClassFile = __CAPPS__;
        if(defined('__PM__')){
            $ClassFile .= __PM__."/Class/". $class_name.'.class.php';
        }else{
            $ClassFile .= "/Class/". $class_name.'.class.php';
        } 
		if ( file_exists( $ClassFile ) ) {
			return require_once( $ClassFile );	
		}           		
    }
    
	return false;
}

/**
| ----------------------------------------------------------
| 加载函数库
| ----------------------------------------------------------
| @param string $name 函数库文件名
| @param string $path 库文件路径
| ----------------------------------------------------------
*/
function import($name,$path = ''){
    $path = $path ? $path : __CFUNC__;
    $FuncFile = $path.'func.php';
    if(file_exists($FuncFile)){
        return require_once( $FuncFile );
    }
    return false;      
}

/**
| ----------------------------------------------------------
| 字符串首字母转成大写
| ----------------------------------------------------------
| @param string $name 字符串
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function parse_name($name) {
    return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
}

/**
| ----------------------------------------------------------
| 获取设置配置
| ----------------------------------------------------------
| @param string $name 字符串
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function C($name=null, $value=null) {
    static $_config = array();
    if (empty($name))   return $_config;
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }
        //二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtolower($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    //设置
    if (is_array($name)){
        return $_config = array_merge($_config, array_change_key_case($name));
    }
    return null;
}

/**
| ----------------------------------------------------------
| 模板解析
| ----------------------------------------------------------
| @param string $File 字符串
| @param string $FileRoot 模板文件夹
| @param string $SkinRoot 模板皮肤
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function T($File, $FileRoot = '', $isFixed = false){
    $template = new CTemplate();
    $tplDir = C('TPL_FILE_DIR');//模板目录   
    $cacheDir = C('TPL_FILE_CACHE'); //缓存生成目录
    
    //指定固定目录
    if($isFixed){}
    //如果设置了模块在公共目录内(公共目录内的模块不支持换肤)
    elseif(defined("IS_STYLE_PUBLIC")){
        $tplDir .= C('TPL_PUBLIC'); 
        $cacheDir .= C('TPL_PUBLIC');  
    }
    //选择默认皮肤目录
    else{
        $tplDir .= C('TPL_FILE_STYLE'); 
        $cacheDir .= C('TPL_FILE_STYLE'); 
    }
    if($FileRoot){
        $tplDir .= $FileRoot.'/'; 
        $cacheDir .= $FileRoot.'/';         
    }elseif(defined('__PM__') && __PM__){
        $tplDir .= __PM__.'/'; 
        $cacheDir .= __PM__.'/';        
    }
    $result = $template->parseTemplate($File,$tplDir,$cacheDir);
    return $result; 
}
/*
| ----------------------------------------------------------
| 模板比对
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function tplcompare($filename,$tpldir,$tplfile,$cache_file,$timestamp){
    $template = new CTemplate();
    $template -> tplcompare($filename,$tpldir,$tplfile,$cache_file,$timestamp);
}
/**
| ----------------------------------------------------------
| 语言包解析
| ----------------------------------------------------------
| @param string $File 字符串
| @param string $FileRoot 模板文件夹
| @param string $SkinRoot 模板皮肤
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function L($name=null){
    static $lang; 
    if (empty($name))   return '';
    $name = explode('.', $name);
    if(!$lang){
        $lang = new CLanguage();
    }
    return $lang->get($name[0],$name[1]);
}

/**
| ----------------------------------------------------------
| 数据持久层
| ----------------------------------------------------------
| @param string $model      
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function D($model = '',$path = ''){
    static $_model; 
    if($_model[$model]){
        return $_model[$model];
    }
    if(empty($model)){
        $model = __PC__;
    }
    if(empty($path)){
        $path = __PM__;
    }
    $model = 'Db'.parse_name($model);

    $path = __CAPPS__.$path.'/DbModel/';
    if(!is_file($path.$model.'.php')){
        return false;
    }
    require_once( $path.$model.'.php' );
    $_model[$model] = new $model();
    return $_model[$model];
}

/**
| ----------------------------------------------------------
| 缓存文件
| ----------------------------------------------------------
| @param string $name 文件名
| @param string $data 缓存数据
| @param string $path 路径
| ----------------------------------------------------------
 */
function CF($name,$data = null, $path = ''){
    static $_cache; 
    if (empty($name))   return '';
    if(!$_cache){
		$_cache = new CCache();
	}
	if(!is_null($data)){
		$_cache->set($name,$data,$path);
	}
    return $_cache->get($name,$path);
}

/**
| ----------------------------------------------------------
| URL地址转换
| ----------------------------------------------------------
| @param string $action 方法
| @param string $controller 控制器
| @param string $model 模块
| ----------------------------------------------------------
 */
function R($action='', $param = '', $controller = '', $model = ''){
    $Url = __DOMAIN__;
    $action = $action ? $action : __PA__;
    $controller = $controller ? $controller : __PC__;   
    $model = $model ? $model : __PM__;
    if(C("DEFAUL_URL_REWRITE")){
        if($model){
            $Url .= $model.'-'.$controller.'/'.$action.($param ? '?'.$param : '');
        }else{
            $Url .= $controller.'-'.$action.($param ? '?'.$param : '');
        }
    }else{
         $Url .= 'index.php?model='.$model.'&amp;controller='.$controller.'&amp;action='.$action.($param ? '&amp;'.$param : '');
    }  
    return strtolower($Url); 
}

/**
| ----------------------------------------------------------
| 字符串转义
| ----------------------------------------------------------
| @param string $name 字符串
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function daddslashes($string, $force = 0) {
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if(!MAGIC_QUOTES_GPC || $force) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = daddslashes($val, $force);
            }
        } else {
            $string = str_replace("'","&acute;",$string);
            $string = addslashes($string);
        }
    }
    return $string;
}

/**
| ----------------------------------------------------------
| 检查目标文件夹是否存在，如果不存在则自动创建该目录
| ----------------------------------------------------------
| @param string $folder 目录路径。不能使用相对于网站根目录的URL
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
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


/**
| ----------------------------------------------------------
| 返回字符串在另一个字符串中第一次出现的位置
| ----------------------------------------------------------
| @param string $haystack 
| @param string $needle 
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function strexists($haystack, $needle) {
    return !(strpos($haystack, $needle) === FALSE);
}


/**
| ----------------------------------------------------------
| 获取客户端IP地址
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL) return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos =  array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip   =  trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

/**
| ----------------------------------------------------------
| JSON模式输出
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function json($outStr, $result = false){
    if(!is_array($outStr)){
        $outStr = array('result' => $outStr,'status' => 100);        
    }else{
        if(!isset($outStr['status'])){
            $outStr['status'] = 100;
        }    
    } 
    if($result){return json_encode($outStr);}
    echo json_encode($outStr);
    exit;  
}

/**
| ----------------------------------------------------------
| 错误输出
| ----------------------------------------------------------
| @return string
| ----------------------------------------------------------
 */
function halt($error){
    $e = array();
    if (!is_array($error)) {
        $trace = debug_backtrace();
        $e['message'] = $error;
        $e['file'] = __PA__;
        $e['class'] = __PA__;
        $e['function'] = __PC__;
        $e['line'] = $trace[0]['line'];
        $traceInfo = '';
        $time = date('y-m-d H:i:m');
        foreach ($trace as $t) {
            $traceInfo .= '[' . $time . '] ' . $t['file'] . ' (' . $t['line'] . ') ';
            $traceInfo .= $t['class'] . $t['type'] . $t['function'] . '(';
            $traceInfo .= implode(', ', $t['args']);
            $traceInfo .=')<br/>';
        }
        if(C('DEFAUL_HALT_TRACE')){
            $e['trace'] = $traceInfo;        
        }
    } else {
        $e = $error;
    }
    if(defined('__AJAXD__') && __AJAXD__){
       echo json_encode(array(
            'file' => __PA__,
            'message' => $error,
            'status' => -1,
        ));
    }else{
        include T(C('TPL_ERROR_FILE'),'Public/Errors',true);        
    }
    exit;
}


?>