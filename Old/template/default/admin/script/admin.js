$(function(){
	$(window).resize(function(){
		initLayout();
	});
	initLayout(); 
	windowScroll();
	isVali();
});

function initLayout(){
	var top = $("#admin_top_block");     //顶站
	var top_nav = $("#nav_child_block");  //子菜单
	var footer = $("#footer_block");      //底部
	var h = top.outerHeight() + top_nav.outerHeight() + footer.outerHeight();
	var height = $(window).height();
	var mt = getCss($("#main_block"),"margin-top");
	var mb = getCss($("#main_block"),"margin-bottom");
	var bt = getCss($("#main_block"),"border-top");
	var bb = getCss($("#main_block"),"border-bottom");
    //设置主框min-hight
	$("#main_block").css({
		'min-height' : (height - h - mb - mt - bt - bb)+'px'
	});
}
