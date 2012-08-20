<?php if(!defined('IN_JAING')){header('HTTP/1.1 404 Not Found');}  
0
|| tplcompare('G:/htdocs/yaoer/template/default/passport/header.htm','G:/htdocs/yaoer/template/default/passport/','G:/htdocs/yaoer/template/default/passport/reg.htm','G:/htdocs/yaoer/data/template/default/passport/reg_33c0ee425e.tpl.php', 1342583671)
|| tplcompare('G:/htdocs/yaoer/template/default/passport/footer.htm','G:/htdocs/yaoer/template/default/passport/','G:/htdocs/yaoer/template/default/passport/reg.htm','G:/htdocs/yaoer/data/template/default/passport/reg_33c0ee425e.tpl.php', 1342583671)
;?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo SYS_URL; ?>template/style/global.css" />
<link rel="stylesheet" type="text/css" href="http://www.yea.com/template/default/passport/style/passport.css" />
<script type="text/javascript">
var CJY = {
	domain : '<?php echo SYS_URL; ?>',
	isLogin : 0,
	flashPath : '<?php echo SYS_URL; ?>static/flash',
	imgPath : '<?php echo SYS_URL; ?>static/image',
	myimgPath : '<?php echo SYS_URL; ?>template/<? echo $this->global['template']['skin']; ?>/<?php echo ROUTE_M; ?>/images',
    lang : {
        'close' : '关闭',
        'ajaxpost' : '提交中...',
        'selectFile' : '选择文件'
    }
};
</script>
<script src="<?php echo SYS_URL; ?>static/js/jquery.js" ></script>
<script src="<?php echo SYS_URL; ?>static/js/common.js" ></script>
<script src="<?php echo SYS_URL; ?>static/js/utils.js" ></script>
<script src="<?php echo SYS_URL; ?>static/js/pop.js" ></script>
<script src="<?php echo SYS_URL; ?>static/js/formValidator.js" ></script>
<script src="http://www.yea.com/template/default/passport/script/passport.js" ></script>
</head>
<body><div class="bg_black" id="bg-black">
	<div class="main_block" id="main-block">
		<div class="abs_block step_1" id="abs-block">
            <form name="passport_reg_form" id="passport_reg_form" action="<?php echo SYS_URL; ?>passport-reg/step" method="post">
                <div class="form_block">
                    <div class="fields">
                        <div class="input"><input type="text" name="xingming" id="xingming" action-value="宝贝姓名" value="" /></div> 
                        <label>亮出你宝贝姓名</label>    
                    </div>  
                    <div class="fields">
                        <div class="input"><input type="text" name="birth" id="birth" action-value="出生年月" value="" /></div> 
                        <label>这个你不会忘了吧？</label>    
                    </div> 
                    <div class="fields">
                        <div class="input">
                        <input type="text" name="showpassword" id="showpassword" action-value="登录密码" value="" />
                        <input type="password" name="password" id="password" value="" style="display:none;" /></div> 
                        <label>需要密码来管理你的宝贝信息</label>    
                    </div> 
                    <div class="fields">
                        <div class="input">
                        <input type="text" name="showconpassword" id="showconpassword" action-value="确认密码" value="" />
                        <input type="password" name="conpassword" id="conpassword" value="" style="display:none;" /></div> 
                        <label>请重复输入一下登录密码</label>    
                    </div>  
                    <!--<div class="fields">
                        <label class="domain"><?php echo SYS_URL; ?></label>
                        <div class="input w80"><input type="text" name="domain" id="domain" action-value="访问地址" value="" /></div> 
                        <label>给你的宝贝系统取个个性的域名吧</label>    
                    </div>--> 
                    <div class="fields">
                        <input type="submit" name="nextBtn" class="input_next" value="下一步" />  
                    </div>  
                </div>
            </form>
        </div>
	</div>
	<div class="footer_bg" id="footer-bg">
		<div class="wo_block"></div>
	</div>
</div></body>
</html>