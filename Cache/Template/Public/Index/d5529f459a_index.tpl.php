<?php if(!defined('IN_CPHP')){header('HTTP/1.1 404 Not Found');}  
0
|| tplcompare('G:/htdocs/yaoer/Template/Public/Head/header.htm','G:/htdocs/yaoer/Template/Public/Index/','G:/htdocs/yaoer/Template/Public/Index/index.htm','G:/htdocs/yaoer/Cache/Template/Public/Index/d5529f459a_index.tpl.php', 1345124776)
;?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><link rel="stylesheet" type="text/css" href="http://www.yea.com/Template/Public/Style/global.css" /><script type="text/javascript">var S = {    domain : '<?php echo DOMAIN; ?>',    flashPath : '<?php echo PUBLIC_FLASH; ?>',    lang : {        'Close' : '关闭',        'ajaxPost' : '提交中...',        'SelectFile' : '选择文件'    }};</script><script type="text/javascript" src="http://www.yea.com/Public/Script/jquery.js"></script><script type="text/javascript" src="http://www.yea.com/Public/Script/common.js"></script><script type="text/javascript" src="http://www.yea.com/Public/Script/utils.js"></script><script type="text/javascript" src="http://www.yea.com/Public/Script/pop.js"></script><script type="text/javascript" src="http://www.yea.com/Public/Script/formValidator.js"></script></head><body><div class="headerblock">    <div class="w1000 clearfix">        <div class="logo"><img src="<?php echo PUBLIC_IMAGES; ?>logo.png" /></div>       </div></div><link rel="stylesheet" type="text/css" href="http://www.yea.com/Template/Public/Index/Style/index.css" /><div class="w1000">    <div class="regbackgroundbox">&nbsp;</div>    <div class="regcontentbox">        <h5>记录咱家宝宝的成长</h5>        <form action="<? echo R();; ?>" method="post">         <div class="fields">            <label>邮箱：</label>            <div class="input inputbox">                <input type="text" name="regmail" id="regmail" value="" action-value="邮箱" />            </div>        </div>        <div class="fields">            <label>&nbsp;</label>            <div class="input">                <span class="btn"><input type="button" name="nextBtn" id="BtnRegeditUser" value="注册" /></span>            </div>        </div>         </form>          <div class="or"></div>        <div class="loginbox">            <h5>您已经有<span>幺儿啊</span>的帐号</h5>            <div class="left">                <a href="javascript:;" class="sync_login_sian">&nbsp;</a>                <a href="javascript:;" class="sync_login_tencent">&nbsp;</a>            </div>            <div class="right">                <span class="btn"><input type="button" name="nextBtn" id="BtnLogin" value="登录" onclick="location.href='<?php echo DOMAIN; ?>caojiayin'"/></span>                </div>        </div>    </div>    </div><script>$(function(){    $(":text")._bindInputFocus();    $("#BtnRegeditUser").click(function(){        location.href = '<?php echo DOMAIN; ?>passport-reg/init';    });    })</script></body></html>