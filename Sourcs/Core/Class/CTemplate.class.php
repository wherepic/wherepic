<?

/**
|---------------------------------------------------------------
| 模板处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
| 加载子模板1 {subtemplate index}
| 加载子模板2 {subtemplate template:skin:index} 模板文件夹template/skin/index文件
| 语言包 {lang myLang}
|---------------------------------------------------------------
*/

class CTemplate{
	private $config = array();
	private $filename ='';
	private $skindir;
	private $tpldir = '';
	private $subtemplates = array();
    private $lang = null;
	public function __construct(){
	}

	public function parseTemplate($File,$tplDir,$cacheDir){
		$this->filename = $File;		
		$this->tpldir = $tplDir;
		$tplfile = $tplDir.$this->filename.C("TPL_SUFFIX");
        makeDir($cacheDir);
		$cacheDir .= substr(MD5($this->filename),5,10).'_'.$this->filename.C("TPL_CACHE_SUFFIX");
		if((@filemtime($tplfile) > @filemtime($cacheDir))){
			$this->__template($tplfile,$cacheDir);	
            clearstatcache();
		}		
		return $cacheDir;
	}

	public function tplcompare($filename,$tpldir,$tplfile,$cachefile,$timestamp){
		$this->tpldir = $tpldir;
		if(@filemtime($filename) > $timestamp){
			$this->__template($tplfile,$cachefile);	
            clearstatcache();
		}				
	}
    
    /*解析规则仿Discuz*/
	private function __template($tpl_file,$cache_file){
		$header = '';
		$subtemplate = false;
		$template = file_get_contents($tpl_file); 
		$pathinfo = pathinfo($tpl_file);
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)"; 
		$this->subtemplates = array();
		for($i = 1; $i <= 3; $i++) {		
			if(strexists($template, '{subtemplate')) {
				$template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{subtemplate\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->__subtemplate('\\2')", $template);
			}			
		}
        $template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{loadjs\s+([a-z0-9_:\/,\.]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->__subloadjs('\\2')", $template);        	
        $template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{loadcss\s+([a-z0-9_:\/,\.]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->__subloadcss('\\2')", $template);        	       
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\=\+\[\]\\\ \-\'\,\%\*\/\.\>\'\"\$\x7f-\xff]+)\}/s", "<?php echo \\1; ?>", $template);        		
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
        $template = preg_replace("/\{lang\s+(.+?)\}/ies", "\$this->__languagevar('\\1')", $template);

        $template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/ies", "\$this->__stripvtags('\\1<? if(\\2) { ?>\\3')", $template);
        $template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "\$this->__stripvtags('\\1<? } elseif(\\2) { ?>\\3')", $template);
        $template = preg_replace("/\{else\}/i", "<? } else { ?>", $template);
        $template = preg_replace("/\{\/if\}/i", "<? } ?>", $template);
		$template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "\$this->__stripvtags('<? \\1 ?>','')", $template);
		$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "\$this->__stripvtags('<? echo \\1; ?>','')", $template); 
        $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->__stripvtags('<? if(is_array(\\1)) foreach(\\1 as \\2) { ?>')", $template);
        $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->__stripvtags('<? if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>')", $template);
        $template = preg_replace("/\{\/loop\}/i", "<? } ?>", $template);

		$template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "\$this->__transamp('\\0')", $template); 
		$template = preg_replace("/\{$const_regexp\}/s", "<?php echo \\1; ?>", $template);
        if(C('DEFAUL_TPL_FORMAT')){
            $template = preg_replace("/[\t\n\r]/ies",'',$template);
        }		
		if(!empty($this->subtemplates)) {
			$headeradd .= "\n0\n";
			foreach($this->subtemplates as $fname) {
				$headeradd .= "|| tplcompare('$fname','$this->tpldir','$tpl_file','$cache_file', ".time().")\n";
			}
			$headeradd .= ';';
		}
		$template = "<?php if(!defined('IN_CPHP')){header('HTTP/1.1 404 Not Found');}  {$headeradd}?>" . $template; 
		if(!file_put_contents($cache_file,$template))return false;
		return true;      		
	}	
	private function __subtemplate($subfile){
        
        if(strexists($subfile,":")){
            $vars = explode(':', $subfile);
            $result = array_chunk($vars,count($vars) - 1);  
            $tpldir = C("TPL_FILE_DIR").(implode('/',$result[0]))."/";
            $file = $tpldir.$result[1][0].C("TPL_SUFFIX");  
        }else{
            $file = $this->tpldir.$subfile.C("TPL_SUFFIX");
        }
		$this->subtemplates[] = $file;
		$content = @implode('', file($file)); 
		return $content;
	} 
	
	private function __subloadcss($Css){
        $TDir = str_replace(__CROOT__, '', C("TPL_FILE_DIR"));
        $publicStyleDir = $TDir.C('TPL_PUBLIC_STYLE');
		$Css = explode(",", $Css);
		$CssFiles = '';
		foreach ($Css as $k => $v) {
			if(strexists($v,":")){
				$R = explode(":", $v);
                $result = array_chunk($R,count($R) - 1);
                $cssD = implode('/',$result[0]);                
				$CssFiles .= "\r\n".'<link rel="stylesheet" type="text/css" href="'.(__DOMAIN__.$TDir.$cssD.'/'.C("TPL_STYLE").'/'.$result[1][0]).'.css" />';
			}else{
				$CssFiles .= "\r\n".'<link rel="stylesheet" type="text/css" href="'.(__DOMAIN__.$publicStyleDir.$v).'.css" />';
			}
		}
		
		return $CssFiles."\r\n";
	}
	private function __subloadjs($Js){
        $publicJSDir = PUBLIC_JS;
		$TDir = str_replace(__CROOT__, '', C("TPL_FILE_DIR"));
		$Js = explode(",", $Js);
		$JsFiles = '';
		foreach ($Js as $k => $v) {
			if(strexists($v,":")){
				$R = explode(":", $v);
                $result = array_chunk($R,count($R) - 1);
                $jsD = implode('/',$result[0]);
				$JsFiles .= "\r\n".'<script type="text/javascript" src="'.(__DOMAIN__.$TDir.$jsD).'/'.C("TPL_SCRIPT").'/'.$result[1][0].'.js"></script>';
			}else{
				$JsFiles .= "\r\n".'<script type="text/javascript" src="'.($publicJSDir.$v).'.js"></script>';
			}
		}
		
		return $JsFiles."\r\n";
	}

	private function __stripvtags($expr, $statement = '') {
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?php \=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}
	 
	private function __transamp($str) {
		$str = str_replace('&', '&amp;', $str);
		$str = str_replace('&amp;amp;', '&amp;', $str);
		$str = str_replace('\"', '"', $str);
		return $str;
	}
    private function __languagevar($var){
        return L($var);
    }	
}
?>