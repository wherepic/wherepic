<?php

/**
|---------------------------------------------------------------
| 分页处理
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/
Class CPaging{
    
    //当前页码   
    public $currPage = 0;
    
    //数据长度
    public $dataNum = 0;
    
    //每页显示的内容条数
    public $pageNums = 20;
    
    //页码链接地址
    public $mpurl = '';
    public $multipage = '';
    private $page = 10;
    
    public function __construct($dataNum, $currPage = 0, $pageNums = 20, $mpurl = ''){
        $this->dataNum = $dataNum;
        $this->currPage = $currPage;
        $this->pageNums = $pageNums;
        if($mpurl){
            $mpurl .= '&amp;'; 
            $mpurl = preg_replace("/page=(\S)*/i", '', $mpurl);    
        }
        $this->mpurl = $mpurl;
        $this->setPages();
    }
    
    /**
    |---------------------------------------------------------------
    | 数组分页
    |---------------------------------------------------------------
    */
    public function getArrayToPage($data){
        $count = count($data);
        $start = (($this->currPage <= 1 ? 0 : $this->currPage - 1) * $this->pageNums);
        $length  = $this->pageNums;
        if(($start + $length)  > $count){
            $length = $count - $start;
        }
        return array(array_slice($data,$start,$length),$this->multipage);
    }
    
    
    private function setPages(){
        $pages = 1;
        if($this->dataNum > $this->pageNums) { 
            $offset = 2;
            $pages = @ceil($this->dataNum / $this->pageNums);
            if($pages <= 1)return;
            if($this->page > $pages) {
                $from = 1;
                $to = $pages;
            } else {
                $from = $this->currPage - $offset;
                $to = $from + $this->page - 1;
                if($from < 1) {
                    $to = $this->currPage + 1 - $from;
                    $from = 1;
                    if($to - $from < $this->page) {
                        $to = $this->page;
                    }
                } elseif($to > $pages) {
                    $from = $pages - $this->page + 1;
                    $to = $pages;
                }
            }

            $multipage = ($this->currPage - $offset > 1 && $pages > $this->page ? '<a '.(defined('isAjax') ? 'href="javascript:;" action-data' : 'href').'="'.$this->mpurl.'page=1" class="first">1 ...</a>' : '').
                ($this->currPage > 1 ? '<a '.(defined('isAjax') ? 'href="javascript:;" action-data' : 'href').'="'.$this->mpurl.'page='.($this->currPage - 1).'" class="prev">上一页</a>' : ''); 
                
            for($i = $from; $i <= $to; $i++) {
                $multipage .= $i == $this->currPage ? '<strong>'.$i.'</strong>' : '<a href="'.$this->mpurl.'page='.$i.'">'.$i.'</a>';
            } 
            
            $multipage .= ($to < $pages ? '<a '.(defined('isAjax') ? 'href="javascript:;" action-data' : 'href').'="'.$this->mpurl.'page='.$pages.'" class="last">... '.$pages.'</a>' : '').
                ($this->currPage < $pages ? '<a '.(defined('isAjax') ? 'href="javascript:;" action-data' : 'href').'="'.$this->mpurl.'page='.($this->currPage + 1).'" class="next">下一页</a>' : '');
            $this->multipage = $multipage ? '<div class="pages">'.$multipage.'</div>' : '';                          
        }
        
        return '';
                       
    }
}
?>