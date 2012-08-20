<?php
define("IS_STYLE_PUBLIC",true);
Class Index {
	public function init(){
		include T("index",'Index');
	}
    
    public function upload(){
        $file = $_FILES['sfile'] ;
        $up = new CUpload();
        $result = $up->Save($file);
        var_dump($result);
        CUpload::moveFile($result['name'],$result['temp'],$result['root']);
    }
}
?>