<?php if(!defined('IN_JAING')){header('HTTP/1.1 404 Not Found');}  
0
|| tplcompare('G:/htdocs/jaing/template/default/file/header.htm','G:/htdocs/jaing/template/default/file/','G:/htdocs/jaing/template/default/file/index.htm','G:/htdocs/jaing/data/template/default/file/index.tpl.php', 1341191222)
;?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo SYS_SITENAME; ?></title>
<link href="<?php echo SYS_URL; ?>template/default/file/css/common.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="bg_line">
<div id="wrap">
<div id="top">
	<div id="logo"><a href="<?php echo SYS_URL; ?>"><img src="<?php echo SYS_URL; ?>template/default/file/images/logo.png" alt="" /></a></div>
</div><div class="login ">
	<div class="login_photo"></div>
	<div class="login_form">
		<p class="login_welcome"><?php echo $lan['login_welcome']; ?></p>
		<p class="login_text"><?php echo $lan['login_text']; ?></p>
		<form method="post" action="<?php echo SYS_URL; ?>m/passport/login">
			<p><input type="text" name="email" class="login_email login_input" value="<?php echo $lan['login_email']; ?>" /></p>
			<p><input type="text" name="password" class="login_pass login_input" value="<?php echo $lan['login_pass']; ?>" /></p>
			<a href="" class="link888 login_form_link"><?php echo $lan['forget_pass']; ?></a>
			<a href="" class="link6a92ab login_form_link"><?php echo $lan['reg_text']; ?></a>
			<input type="submit" value="<?php echo $lan['login_form_button']; ?>" class="login_button" />
		</form>
	</div>
	<div class="yaoera_one"><?php echo $lan['yaoerais']; ?></div>
</div>
</div>
</div>
<div id="footer">
	<div class="footer_house"></div>
	<div class="footer_lang"></div>
	<div class="footer_ge"></div>
</div>
</body>
</html>