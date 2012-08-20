<?php

/**
|---------------------------------------------------------------
| 文件缓存处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

Class CFile{
	
	private $prefix = '~';
	public function __construct() {}	

	/**
	|---------------------------------------------------------------
	| 获取缓存写入目录
	|---------------------------------------------------------------
	| @param mixed $name 缓存名称
	| @param string $data 缓存数据
	|---------------------------------------------------------------
	*/
	public function getPath($name = 'a', $path = ''){
		$filepath = C("DATA_CACHE_PATH").($path ? $path.'/' : '');
		$filename = $this->prefix.MD5($name).'.cache.php';	
		if(!is_dir($filepath)) {
			makeDir($filepath, 0777, true);
		}
		return $filepath.$filename;
	}

	/**
	|---------------------------------------------------------------
	| 写入缓存
	|---------------------------------------------------------------
	| @param mixed $name 缓存名称
	| @param string $data 缓存数据
	|---------------------------------------------------------------
	*/
	public function set($name, $data, $path = ''){
		$filepath = $this->getPath($name, $path);
		$data = serialize($data);
		if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
			$data	=	gzcompress($data,3);
		}
		$check = '';
		if(C("DATA_CACHE_CHECK")){
			$check  =  md5($data);
		}
		$data    = "<?php\n".(date("Y-m-d H:i:s"))."\n{$check}".$data."\n?>";
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
	public function get($name,$path = ''){
		$filepath = $this->getPath($name, $path);
		if(!is_file($filepath)){
			return false;
		}
		$content    =   file_get_contents($filepath);
		if( false !== $content) {

			if(C("DATA_CACHE_CHECK")){
				$check  =  substr($content,26, 32);
				$content   =  substr($content,58, -3);
				//$content   =   gzuncompress($content);
                if($check != md5($content)) {//数据校验
                    return false;
                }
			}else{
				$content   =  substr($content,26, -3);	
			}
            if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                //启用数据解压
                $content   =   gzuncompress($content);
            }	
			return unserialize($content);
		}
		return false;
	}
	
	/**
	* 删除缓存
	* 
	* @param mixed $name
	* @return bool
	*/
	public function delete($name,$path) {   
        $filepath = $this->getPath($name, $path);
		if(is_file($filepath)) {
			return @unlink($filepath) ? true : false;
		} else {
			return false;
		}
	}
    
    public function flush(){
        return ;
    }	
	
}

?>