<?php

/*

|---------------------------------------------------------------
| 文件缓存处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------

*/

class cache_file{
	private $_config = null;
	public function __construct() {
		$_config = main::getConfig('global');
		$this->_config = $_config['cache'];
	}	
	
	/**
	* 写入缓存
	* 
	* @param mixed $name 缓存名称
	* @param string $data 缓存数据
	* @param mixed $type 缓存类型  array数组，serialize序列化
	* @return int
	*/
	public function set($name, $data, $type = 'array'){
		$filepath = JAING_PATH.$this->_config['file']['data'];
		$filename = $name.'.cache.php';

		if(!is_dir($filepath)) {
			makeDir($filepath, 0777, true);
		}

		if($type == 'array'){
			$data = "<?php\n//system cache file, DO NOT modify me!\n//Created: ".date("Y-m-d H:i:s")."\n//filename : $filename \n \nreturn ".var_export($data, true).";\n?>";
		}elseif($type == 'serialize') {
			$data = serialize($data);
		}
		$file_size = file_put_contents($filepath.$filename, $data, LOCK_EX);
		return $file_size ? $file_size : 'false';				
	}
	
	/**
	* 获取缓存
	* 
	* @param mixed $name
	* @param mixed $type
	* @return mixed
	*/
	public function get($name, $type = 'array'){
		$filepath = JAING_PATH.$this->_config['data'];
		$filename = $name.'.cache.php';
		if (!file_exists($filepath.$filename)) {
			return false;	
		}
		if($type == 'array'){
			$data = @require($filepath.$filename);
		}elseif($type == 'serialize') {
			$data = unserialize(file_get_contents($filepath.$filename));
		}	
		return $data;
	}
	
	/**
	* 删除缓存
	* 
	* @param mixed $name
	* @return bool
	*/
	public function delete($name) {   
		$filepath = JAING_PATH.$this->_config['data'];
		$filename = ROUTE_M.'_'.$name.'.cache.php';
		if(file_exists($filepath.$filename)) {
			return @unlink($filepath.$filename) ? true : false;
		} else {
			return false;
		}
	}	
	
}

?>