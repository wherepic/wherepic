<?

/*
|---------------------------------------------------------------
| 模板处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Modified 2012-07-18
|---------------------------------------------------------------
| 加载子模板1 {subtemplate index}
| 加载子模板2 {subtemplate template/skin:index} 模板文件夹template/skin/index文件
| 语言包 {lang myLang}
|---------------------------------------------------------------
*/

class template{
	private $config = array();
	private $filename ='';
	private $skindir;
	private $tpldir = '';
	private $subtemplates = array();
    private $lang = null;
	public function __construct(){
		$sys = main::getConfig('global');
		$this->config = $sys['template'];
        $this->lang = new language();
	}

	public function parseTemplate($filename,$skindir = 'default' ,$tpldir = ''){
		$this->filename = $filename;
		$this->skindir = $skindir;
		if(!$tpldir){
			$tpldir = JAING_PATH.$this->config['tpldir'].$skindir."/";
			if(defined('ROUTE_M')){
				$tpldir .= ROUTE_M."/";
			}
		}
		$this->tpldir = $tpldir;
		$tplfile = $tpldir.$filename.'.htm';
		$cachefile = JAING_PATH.$this->config['tplcache'].$skindir."/";
		if(defined('ROUTE_M')){
				$cachefile .= ROUTE_M."/";
		}
		makeDir($cachefile);
        $f = $filename."_".substr(MD5($filename),0,10);
        $cachefile .= $f.".tpl.php"; 
        echo "<!--".@filemtime($tplfile) .",".@filemtime($cachefile)."-->";
		if((@filemtime($tplfile) > @filemtime($cachefile))){
			$this->__template($tplfile,$cachefile);	
		}		
		return $cachefile;
	}

	public function tplcompare($filename,$tpldir,$tplfile,$cachefile,$timestamp){
		$this->tpldir = $tpldir;
		if(@filemtime($filename) > $timestamp){
			$this->__template($tplfile,$cachefile);	
		}				
	}

	private function __template($tpl_file,$cache_file){
		$header = '';
		$subtemplate = false;
		$template = file_get_contents ($tpl_file); 
		$pathinfo = pathinfo($tpl_file);
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)"; 
		$this->subtemplates = array();
		for($i = 1; $i <= 3; $i++) {
			if(strexists($template, '{subtemplate')) {
				$template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{subtemplate\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->__subtemplate('\\2')", $template);
			}
			if(strexists($template, '{loadcss')) {
				$template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{loadcss\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->__subloadcss('\\2')", $template);
			}
			if(strexists($template, '{loadjs')) {
				$template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{loadjs\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->__subloadjs('\\2')", $template);
			}
		}		       
		//$template = preg_replace("/[\n\r\t]*\{subtemplate\s+([^\{\}]+)\}[\n\r\t]*/ies", "\$this->__subtemplate(\$pathinfo,'\\1',\$header)", $template);
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\=\+\[\]\\\ \-\'\,\%\*\/\.\>\'\"\$\x7f-\xff]+)\}/s", "<?php echo \\1; ?>", $template);        		
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
        $template = preg_replace("/\{lang\s+(.+?)\}/ies", "\$this->__languagevar('\\1')", $template);
		//$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
		$template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "\$this->__stripvtags('\\1<?php } elseif(\\2) { ?>\\3','')", $template);
		$template = preg_replace("/([\n\r\t]*)\{else\}([\n\r\t]*)/is", "\\1<?php } else { ?>\\2", $template);
		$template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "\$this->__stripvtags('<? \\1 ?>','')", $template);
		$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "\$this->__stripvtags('<? echo \\1; ?>','')", $template);            
		for($i = 0; $i < 12; $i++) {	
			$template = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)\{\/loop\}/ies", "\$this->__stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<? } } ?>')", $template);        
			$template = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)\{\/loop\}/ies", "\$this->__stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<?php } } ?>')", $template);                           
			$template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/ies", "\$this->__stripvtags('\\1<?php if(\\2) { ?>\\3','\\4\\5<?php } ?>\\6')", $template);
		}
		$template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "\$this->__transamp('\\0')", $template); 
		$template = preg_replace("/\{$const_regexp\}/s", "<?php echo \\1; ?>", $template);
		//$template = preg_replace("/[\n\r\t]/ies",'',$template);
				
		if(!empty($this->subtemplates)) {
			$headeradd .= "\n0\n";
			foreach($this->subtemplates as $fname) {
				$headeradd .= "|| tplcompare('$fname','$this->tpldir','$tpl_file','$cache_file', ".time().")\n";
			}
			$headeradd .= ';';
		}
		$template = "<?php if(!defined('IN_JAING')){header('HTTP/1.1 404 Not Found');}  {$headeradd}?>" . $template; 

		if(!file_put_contents($cache_file,$template))return false;
		return true;      		
	}	
	private function __subtemplate($subfile){
		$vars = explode(':', $subfile);
		$tpldir = '';
		if(count($vars) > 1){
			$tpldir = JAING_PATH.$this->config['tpldir'].$vars[0]."/";
			$file = $tpldir.$vars[1].'.htm';
		}else{
			$file = $this->tpldir.$subfile.'.htm';
		}
		$this->subtemplates[] = $file;
		$content = @implode('', file($file)); 
		return $content;
	} 
	
	private function __subloadcss($cssfile){
		$dir = str_replace(JAING_PATH, '', $this->tpldir);
		$cssfile = SYS_URL.$dir.'style/'.$cssfile;
		return "\r\n".'<link rel="stylesheet" type="text/css" href="'.$cssfile.'.css" />'."\r\n";
	}

	private function __subloadjs($jsfile){
		$dir = str_replace(JAING_PATH, '', $this->tpldir);
		$jsfile = SYS_URL.$dir.'script/'.$jsfile;
		return "\r\n".'<script src="'.$jsfile.'.js" ></script>'."\r\n";
	}

	private function __stripvtags($expr, $statement) {
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
        return $this->lang->get($var);
    }	
}
?>