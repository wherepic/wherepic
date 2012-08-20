$(function(){
	$(window).resize(function(){
		initLayout();
	});
	initLayout();
    $(":text,:password,textarea")._bindInputFocus();            
});

function initLayout(){
	var minHeight = 500;
	var bg = $("#bg-black"); 
	var main = $("#main-block"); 
	var abs = $("#abs-block"); 
	var footer = $("#footer-bg"); 
	var height = $(window).height();
	var width = $(document).width();
	height = height > minHeight ? height : minHeight;
	bg.css({
		'_height' : height+'px',
		'min-height' : height+'px'
	})
	main.css({
		'_height' : (parseInt((height - abs.height()) / 2) + abs.height())+'px',
		'min-height' : (parseInt((height - abs.height()) / 2) + abs.height())+'px'
	});
	abs.css({
		'top' : parseInt((height - abs.height()) / 2) - 15 +'px',
		'left' : parseInt((width - abs.width()) / 2) +'px'
	});
	footer.css({
		'top' : (height - footer.height())+'px'
	});
}
