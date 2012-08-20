<?php

/**
|---------------------------------------------------------------
| 文件上传处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class CUpload{
    private $fileExt = '';
    private $file;
    public $Error;
    private $session = '';
    public function __construct(){
        $this->session = new CSession();
    }
    
    /**
    |---------------------------------------------------------------
    | 保存上传文件
    |---------------------------------------------------------------
    | @param mixed $file 上传文件数组
    | @param mixed $newName 重命名
    | @param mixed $fileExt 可上传文件类型
    | @param mixed $fileMaxSize 上传文件大小MB
    | @param mixed $upFileRoot //文件保存路径
    |---------------------------------------------------------------
    */ 
    public function Save($file, $newName = '', $fileExt = '', $fileMaxSize = '', $upFileRoot = ''){
        $this->file = $file;
        $error = '';
        if(!is_array($file)){
            halt(L("UP_FILE_EXISTS"));
            return false;
        }
        if(!$this->chkError($file['error'])){
            halt('UPLOAD FILE ERROR CODE : '.$file['error']);
            return false;  
        }
        if(!$this->chkType($file['name'],$fileExt)){
            halt(L("UP_FILE_EXT"));
            return false;       
        }
        if(!$this->chkSize($file['size'],$fileMaxSize)){
            halt(L("UP_FILE_EXT"));
            return false;       
        }
        
        $fileName = $this->getFileName($file, $newName); 
        $saveRoot = $this->getSaveRoot($upFileRoot);
        if($saveRoot['temp']){
            makeDir($saveRoot['temp']);
            if (!move_uploaded_file($file['tmp_name'], $saveRoot['temp'].$fileName)){
                halt(L("UP_FILE_SAVE").'[TEMP]');
                return false;
            }        
        }else{
            makeDir($saveRoot['root']);
            if (!move_uploaded_file($file['tmp_name'], $saveRoot['root'].$fileName)){
                halt(L("UP_FILE_SAVE"));
                return false;
            }             
        }
        $result = array(
            'name' => $fileName,
            'ext' => $this->fileExt,
            'root' => $saveRoot['root'],
            'temp' => $saveRoot['temp'],
            'size' => $file['size'],
       );
       $this->session->set(MD5($fileName),$result);
       return  $result;  
    }

   /**
    |---------------------------------------------------------------
    | 移动文件
    |--------------------------------------------------------------- 
    */    
    public static function moveFile($file,$sorcus,$dest){
        if(is_file($sorcus.$file)){
            makeDir($dest);
            if(@rename(($sorcus.$file),($dest.$file))){
                //@unlink($sorcus.$file);
                return true;
            }
        } 
        return false;   
    }

   /**
    |---------------------------------------------------------------
    | 获取新文件名
    |--------------------------------------------------------------- 
    */    
    public function getFileName($file,$newName){
        if($newName){
            $newName .= '.'.$this->fileExt;
        }elseif(C("UP_RENAME")){
            $newName = ( $this->randFilename().'.'.$this->fileExt);
        }else{
            $newName = $file['name'];
        } 
        return $newName;   
    }

   /**
    |---------------------------------------------------------------
    | 验证文件类型
    |--------------------------------------------------------------- 
    */    
    public function chkType($fileName,$fileExt){
        if(C("UP_VER_TYPE") == 'sign'){
            return $this->SignVerif($fileName,$fileExt);
        }else{
            return $this->ExtVerif($fileName,$fileExt);
        }
    }
    
    /**
    |---------------------------------------------------------------
    | 以文件签名方法验证上传文件类型
    |--------------------------------------------------------------- 
    */    
    public function SignVerif($fileName,$fileExt){
        if(!$this->ExtVerif($fileName,$fileExt)){return false;}
        $extname = daddslashes(strtolower(substr(strrchr($fileName, '.'), 1, 10))); 
        $sign = $this->getTypeList($extname);  
        $sign = explode(",",$sign);
        $file = @fopen($this->file['tmp_name'],"rb");
        if(!$file) halt(L("UP_FILE_OPEN"));
        $bin = fread($file, 50);
        fclose($file);
        foreach($sign As $k => $v){
            $blen = strlen(pack("H*",$v));
            $tbin = substr($bin,0,intval($blen));
            $nsign = array_shift(unpack("H*",$tbin));            
            if(strtolower($v) == strtolower($nsign)){
                return true;
            } 
        }

        return false;
    }
    /**
    |---------------------------------------------------------------
    | 以文件扩展名方法验证上传文件类型
    |---------------------------------------------------------------
    */    
    public function ExtVerif($fileName,$fileExt){ 
      
        //if(!is_file($fileName)){halt(L("UP_FILE_EXISTS"));}       
        $extname = daddslashes(strtolower(substr(strrchr($fileName, '.'), 1, 10))); 
        $this->fileExt = $extname;    
        $type = $fileExt ? $fileExt : C("UP_FILE_EXT");
        $type = explode(",",$type); 
        if(in_array($extname,$type)){
            return true;
        }
        return false; 
    }
    
    /**
     |---------------------------------------------------------------
     | 得到文件头与文件类型映射表*
     |---------------------------------------------------------------
     */
    public function getTypeList($ext){
        
        $TypeList = array(
            'jpg'   => 'FFD8FFE1,FFD8FFE0',
            'png'   => '89504E47',
            'gif'   => '47494638',
            'bmp'   => '424D',
            'tif'   => '49492A00',
            'dwg'   => '41433130',
            'psd'   => '38425053',
            'rtf'   => '7B5C727466',
            'xml'   => '3C3F786D6C',
            'html'  => '68746D6C3E',
            'eml'   => '44656C69766572792D646174',
            'dbx'   => 'CFAD12FEC5FD746F',
            'pst'   => '2142444E',
            'xls'   => 'D0CF11E0',
            'doc'   => 'D0CF11E0',
            'docx'  => '504B0304',
            'xlsx'  => '504B0304',
            'pptx'  => '504B0304',
            'wpd'   => 'FF575043',           
            'eps'   => '252150532D41646F6265',
            'ps'    => '252150532D41646F6265',
            'pdf'   => '255044462D312E',
            'zip'   => '504B0304',
            'rar'   => '52617221',
            '7z'    => '377ABCAF',
            'wav'   => '57415645',
            'avi'   => '41564920',
            'ram'   => '2E7261FD',
            'rm'    => '2E524D46',
            'mpg'   => '000001BA,000001B3',
            'mov'   => '6D6F6F76',
            'asf'   => '3026B2758E66CF11',
            'mid'   => '4D546864',
            'mp3'   => '49443303',
            'ttf'   => '00010000', 
            'swf'   => '435753', 
            //'exe'   => '4D5A', 
        );
        return $TypeList[$ext];
    }
    
    /**
     |---------------------------------------------------------------
     | 上传文件错误检查
     |---------------------------------------------------------------
     */    
    private function chkError($error){
        return ($error == UPLOAD_ERR_OK)?true:false; 
    }
    
    /**
     |---------------------------------------------------------------
     | 上传文件大小检查
     |---------------------------------------------------------------
     */  
    private function chkSize($size,$maxSize){
        $maxSize = $maxSize ? $maxSize : C("UP_MAXSIZE");
        $maxSize = $maxSize * 1024 * 1024;
        return (($size > 0 && $maxSize >= $size) ? true : false );
    } 
    
    /**
     |---------------------------------------------------------------
     | 生成文件名
     |---------------------------------------------------------------
     */
    private function randFilename(){
        $string = 'abcdefghijklmnopgrstuvwxyz0123456789';
        $rand = ''; 
        for ($x=0;$x<10;$x++){$rand .= substr($string,mt_rand(0,strlen($string)-1),1);} 
        return MD5(date("YmdHis").$rand);       
    }    
    
    /**
     |---------------------------------------------------------------
     | 文件上传保存路径
     |---------------------------------------------------------------
     */
    private function getSaveRoot($upFileRoot){
        $tRoot = '';       
        if($upFileRoot){
            $tRoot = __CUPLOAD__.$upFileRoot.'/';
        }else{
            $tRoot = __CUPLOAD__.parse_name($this->getFileGroup()).'/';
        }
        $cRoot = date("Y").'/'.date("m").'/'.date("d").'/'; 
        return array(
            'temp' => C("UP_SAVE_TEMP") && C("UP_TEMP_ROOT") ? (C("UP_TEMP_ROOT").date("Ymd").'/') : '',
            'root' => $tRoot.$cRoot,
        );            
    }
    
    private function getFileGroup(){
        $fg = '';
        $Group = array(
            'images' => array('jpg','png','gif','bmp','tif'),
            'music' => array('mp3'),
            'video' => array('wav','ram','rm','mpg','mov','asf','mid','swf'),
        );
       foreach($Group as $k => $v){
           if(in_array(strtolower($this->fileExt),$v)){
            $fg = $k;
            break;
           }
       }
       return $fg ? $fg : 'files';
    }
}

?>