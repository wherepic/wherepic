$(function(){
	$(window).resize(function(){
		initLayout();
	});
	initLayout();
   
    $(".step_1 input").each(function(){
        var v = $(this).attr("action-value");
        var type = $(this).attr("name");
        if(!$(this).val())$(this).val(v);
        $(this).focus(function(){
            if(type == 'showpassword'){
                $(this).hide();
                $("#password").show().focus();   
            }
            if(type == 'showconpassword'){
                $(this).hide();
                $("#conpassword").show().focus();   
            }
            if(v == $(this).val()){
                $(this).val('') ;  
            }
        }).blur(function(){
            if(type == 'password'){
                if($(this).val() == ''){
                   $(this).hide(); 
                   $("#showpassword").show().val($("#showpassword").attr("action-value")); 
                }    
            }else if(type == 'conpassword'){
                if($(this).val() == ''){
                   $(this).hide(); 
                   $("#showconpassword").show().val($("#showconpassword").attr("action-value")); 
                }    
            }else if($(this).val() == ''){
                $(this).val(v) ;
            }     
        });
    });
    
    $("#passport_reg_form")._submitForm(function(){
        var flag = $("#xingming").vaildator({onerror:'{lang js_sitename_empty}',min:2,isEmpty:true});
        return false;
    },function(result){
        location.reload();
        //$(document).scrollTop(0);
    });     
});

function initLayout(){
	var minHeight = 500;
	var bg = $("#bg-black"); 
	var main = $("#main-block"); 
	var abs = $("#abs-block"); 
	var footer = $("#footer-bg"); 
	var height = $(window).height();
	var width = $(window).width();
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
		'top' : parseInt((height - abs.height()) / 2) - 30 +'px',
		'left' : parseInt((width - abs.width()) / 2) +'px'
	});
	footer.css({
		'top' : (height - footer.height())+'px'
	});
}
