(function($){
	$.fn.pop = function(opt){
		var options = $.extend({
			thisID : 'popid',
			only : true,
			isAutoClose : true,
			time : 5000,
			cat : 'succ', //succ,error,warn,info
			type : 1,
			content : ''
		}, opt);
		var $this = $(this);
		var obj = $('<div></div>');
		if(options.type == 1){
			obj.addClass("dialog_pop");
			var container = $('<div class="pop_blcok pop_'+options.cat+'_blcok"><div class="ico_block"></div><span>'+options.content+'</span></div>');
			container.appendTo(obj);
			obj.appendTo($("#popTips_block")).fadeIn('fast');
			setTimeout(function(){
				obj.slideUp('fast');
			},options.time);
		}
		//window['JAINGPOP_'+options.thisID] = this;
	}
})(jQuery);