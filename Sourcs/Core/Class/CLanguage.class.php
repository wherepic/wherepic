<?php

/**
|---------------------------------------------------------------
| 语言包处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class CLanguage{
    private $type;
    private static $LangArray = array();
    public function __construct(){
		if(!$this->LangArray){
			$this->type = C("DEFAUL_C_LANG") ? C("DEFAUL_C_LANG") : 'zh_cn';  
			$this->LangArray = array_merge($this->import('global'),$this->import(__PC__,__PM__));
		}
    }

    private function import($LangFile,$ROOT = ''){
        $File = __CLANG__.$this->type.'/';
        if($ROOT){
            $File .= $ROOT.'/';
        }         
        if(file_exists($File.$LangFile.'.lang.php')){
            return include $File.$LangFile.'.lang.php';
        } 
        return array();      
    }
        
    /**
    |---------------------------------------------------------------
    | 获取语言包
    |--------------------------------------------------------------- 
    */
    public function get($var,$key = ''){   
       $lang = $this->LangArray;  
       if($key){
           $lang = $lang[$key];
       }         
       if(isset($lang[$var])){
           return  $lang[$var];
       }else{
           return $var;
       }     
    }
}

?>