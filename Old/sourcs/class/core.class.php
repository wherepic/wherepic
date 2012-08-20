<?php

/*
|---------------------------------------------------------------
| 系统核心类，是所有控制器，自定义类的父类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

//error_reporting(0);

class core {
	public $global,$db,$session,$cache,$lang;
	public function __construct(){
		$this->global = main::getConfig("global");
		$this->db = new table();
		$this->session = new session();
		$this->cache = $this->cache_factory();
        $this->lang = new language();
		
        define('SYS_URL', $this->global['domain']);
	}

	/*
	* tplfile : 模板文件名
	* tpldir : 指定模板路径
	*/
	public function template($tplfile,$tpldir = ''){
		$template = new template();
		return $template->parseTemplate($tplfile,'default',$tpldir);
	}
	
	public function cache_factory(){
		$obj = null;
		$cache = $this->global['cache'];
		$type = $cache['mode'];
		if($type == 'memcache'){
			define('MEMCACHE_HOST', $cache['memcache']['hostname']);
			define('MEMCACHE_PORT', $cache['memcache']['port']);
			define('MEMCACHE_TIMEOUT', $cache['memcache']['timeout']);
			define('MEMCACHE_DEBUG', $cache['memcache']['debug']);	
			$obj = new cache_memcache();		
		}else{
			$obj = new cache_file();
		}
		return $obj;	
	}

	public function getPostUrl($func,$class='',$model=''){
		$isRewrite = $this->global['isRewrite'];
		$model = $model ? $model : ROUTE_M;
		$class = $class ? $class : ROUTE_C;
		$url = '';
		if($model && $class && $isRewrite){
			$url .= "$model-$class/$func";
		}elseif(!$isRewrite){
			$url .= "index.php?model=$model&controller=$class&method=$func";
		}
		return $url;
	}

	/*
	$message : 输出信息
	$type : 输出方式(ajax,page)
	$status : 状态(succ,error,warn,info)
	$url
	*/
	public function showmessage($messgae, $type = 'ajax', $status ='succ', $url = ''){
		$out = array(
			'message' => $messgae,
			'status' => $status,
			'url' => $url,
		);		
		if($type == 'ajax'){
			echo json_encode($out);
		}else{
			$this->session->set("poptips",$out);
			if($url){
				header("Location:$url");
			}
		}
		exit;
	}

	function crypt($string, $operation = 'DECODE', $key = '', $expiry = 0) {

		$ckey_length = 4;

		$key = md5($key ? $key : $this->global['hash']['key']);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}



}
?>