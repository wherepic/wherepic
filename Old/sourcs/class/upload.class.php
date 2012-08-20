<?php

/*
|---------------------------------------------------------------
| 文件上传类类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Modified 2011-02-10
|---------------------------------------------------------------
*/

class upload{
	
	/**
	* 上传文件
	* 
	* @var mixed
	*/
	private $file = '' ;
	private $upfileSize = '';
	private $upfileType = '';
	private $upfilePath = '';
	private $newfilename = '';
	private $ext = '';
	
	public $error = '';
	
	public function __construct(){
	
	}
	
	/**
	* 初始化上传
	* 
	* @param mixed $file  上传的文件
	* @param mixed $nfilename 新命名
	* @param mixed $upfileSize 上传文件大小
	* @param mixed $upfileType  上传文件类型
	* @param mixed $upfilePath  存放路径
	* @param mixed $randfilename 随机命名文件,$nfilename为空时有效
	* @return upload
	*/
	public function start($file, $nfilename = '', $upfileSize = 0, $upfileType = array(), $_upfilePath = '', $randfilename = true) {
		$config = main::getConfig('global');
		$config = $config['upload'];
		
		$this->file = $file;
		$this->upfileSize = $upfileSize ? $upfileSize : $config['size']; 
		$this->upfileType = count($upfileType) > 0 ? $upfileType : $config['ext'];
		$this->upfilePath = $_upfilePath ? $_upfilePath : $config['root'];
		$this->randfilename = $randfilename;
		$this->newfilename = $nfilename;
		return $this->__upload();
	}
	
	/**
	* 执行上传
	* 
	*/
	private function __upload(){
		
		$f = $this->file;

		if(!is_array($f)){
			$this->error = '未知文件或者未找到所有上传的文件信息。';
			return false;
		}
		if(!$this->chkError($f['error'])){
			$this->error = '上传文件失败:'.$f['error'];
			return false;  
		}
		if(!$this->chkType($f['name'])){
			//$this->error = '上传文件失败,不支持该文件类型'.$this->ext.'。<br/> 支持的文件类型有：'.(implode(",",$this->upfileType));
			//return false;  
		}
		if(!$this->chkSize($f['size'])){
		//	$this->error = '上传文件失败，文件大小限制为:'.(round($this->upfileSize/ 1024,2)).' MB。<br/>当前上传的文件大小为:'.(round(($f['size'] / 1024 / 1024),2))." MB";
			//return false;  
		}
		
		$attdir = JAING_PATH.$this->upfilePath.date("Ymd")."/";
		if(!is_dir($attdir)){$this->make_dir($attdir);}
		
		$newfilename = '';
		
		if(!empty($this->newfilename)){
			$newfilename = $this->newfilename.'.'.$this->ext;        
		}elseif($this->randfilename){
			$newfilename = $this->getFilename().'.'.$this->ext;
		}else{
			$newfilename = $f['name'];
		}

		if (!move_uploaded_file($f['tmp_name'], $attdir.$newfilename)){
			$this->error = '将上传的文件移动到新位置失败。';
			return false;
		}

		$newfilename = str_replace(JAING_PATH,'',$attdir.$newfilename);
		return $newfilename; 
	}
	
	/**
	* 类型检查
	* 
	* @param mixed $type
	*/
	private function chkType($filename){
		$this->ext = application::daddslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
		foreach($this->upfileType As $key => $val){
			if($val == $this->ext){
				return true;
			}
		}
		return false;
	}
	
	/**
	* 文件大小检查
	* 
	* @param mixed $size
	*/
	private function chkSize($size){
	
		return (($size > 0 && $this->upfileSize * 1024 >= $size) ? true : false );
	}
	
	/**
	* 错误检查
	* 
	* @param mixed $error
	*/
	private function chkError($error){
		return ($error == UPLOAD_ERR_OK)?true:false; 
	}
	
	/**
	* 获取随机文件名字
	* 
	*/
	private function getFilename(){
		$string = 'abcdefghijklmnopgrstuvwxyz0123456789';
		$rand = ''; 
		for ($x=0;$x<10;$x++){$rand .= substr($string,mt_rand(0,strlen($string)-1),1);} 
		return MD5(date("YmdHis").$rand);       
	}
	
	function GrabImage($url,$filename="") { 
		if($url==""):return false;endif; 
		if($filename=="") { 
			$ext=strrchr($url,"."); 
			if(empty($ext))$ext = '.jpg';
			$filename=$this->getFilename().$ext; 
		}
		$_temp = date("Ymd")."/";
		$this->make_dir(JAYN_IMAGE_PATH.$_temp);
		echo JAYN_IMAGE_PATH.$_temp.$filename;
		ob_start(); 
		readfile($url); 
		$img = ob_get_contents(); 
		ob_end_clean(); 
		$size = strlen($img); 
		$fp2=@fopen(JAYN_IMAGE_PATH.$_temp.$filename, "a"); 
		fwrite($fp2,$img); 
		fclose($fp2); 
		return $filename; 
	} 	
	
	/**
	 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
	 *
	 * @access      public
	 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
	 *
	 * @return      bool
	 */
	public function make_dir($folder){
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
		
}
?>