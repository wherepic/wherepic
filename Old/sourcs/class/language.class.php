<?php

/*
|---------------------------------------------------------------
| 语言包处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class language{
    private $config;
    private $lang; //JAING_LANG_PATH
    public function __construct(){
        $this->config = main::getConfig('global');   
        
        $langPath = JAING_LANG_PATH.$this->config['language'];
        $global_lang = include "$langPath/global.lang.php";
        $controllers_lang_file = $langPath.'/'.ROUTE_M.'/'.ROUTE_C.'.lang.php';
        $controllers_lang = array();
        if(file_exists($controllers_lang_file)){
            $controllers_lang = include $controllers_lang_file;
        }      
        $this->lang = array_merge($global_lang,$controllers_lang); 
    }
    
    /**
    * 获取语言包
    * 
    */
    public function get($var,$key = ''){   
       $lang = $this->lang;  
       if($key){
           $lang = $lang[$key];
       }         
       if(isset($lang[$var])){
           return  $lang[$var];
       }else{
           return "!".$var."!";
       }     
    }
}

?>