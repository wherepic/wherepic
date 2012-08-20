/**
* @author C.JAING <c.jaing@gmail.com>
*		
调用方法
$(".hd_photos").cScroll({
			sBox : $(".hd_photos").find("ul"),
			sChild : $(".hd_photos").find("li"),
			sCount : $(".hd_photos").find("li").length,
			aLeft : $(".button_left"),
			aRight : $(".button_right"),
			isDian : {'box':'#hd_ding_box','classname':'hd_ding_dark','focusclassname':'hd_ding_light'},
			sColumn : 1,
			sLoop : true, //循环滚动
			sAuto:true //自动滚动
		});
*
*isDian : {'box':'#hd_ding_box','classname':'hd_ding_dark','focusclassname':'hd_ding_light'},
box:滚动点的标识父对象
classname:点的默认样式
focusclassname :当前（焦点）点的样式
*
*CSS 写法
* .hd_photos 滚动模块父对象，必须要有position:relative属性
* .hd_photos ul 必须要有position:absolute属性,UL是作为滚动模块
----------------------------
.hd_photos{width:570px;height:270px;overflow:hidden;margin-left:16px;margin-top:16px;position:relative;}
.hd_photos ul{width:5000px;height:270px;position:absolute;}
.hd_photos ul li{width:570px;height:270px;float:left;position:relative;}
.hd_photos_mask{height:36px;width:570px;background-color:#000;filter:alpha(opacity=50);opacity:0.5;position:absolute;left:0px;top:233px;border:1px solid #000;}
.hd_photos_desc{position:absolute;height:24px;line-height:24px;top:239px;left:22px;color:#fff;}
-------------------------------------
<div class="hd_photos">
	<ul>
		<li>
			<a href="#"><img src="1.jpg" alt="" /></a>
			<div class="hd_photos_mask"></div>
			<div class="hd_photos_desc">第一张图片</div>
		</li>
		<li>
			<a href="#"><img src="2.jpg" alt="" /></a>
			<div class="hd_photos_mask"></div>
			<div class="hd_photos_desc">第二张图片</div>
		</li>
		<li>
			<a href="#"><img src="3.jpg" alt="" /></a>
			<div class="hd_photos_mask"></div>
			<div class="hd_photos_desc">第三张图片</div>
		</li>
	</ul>
</div>
<ul id="hd_ding_box">
	<li class="hd_ding_light"></li>
	<li class="hd_ding_dark"></li>
	<li class="hd_ding_dark"></li>
</ul>
**/
(function($) {

	$.fn.cScroll = function(options){
		var $this = $(this);
		var C = this;
		var _default = {
			aLeft : null, //向左对象
			aUp : null, //向上对象
			aRight : null, //向右对象
			aDown : null, //向下对象
			aStyle : false,
			sBox :  null, //滚动对象父	
			sChild : null,
			sWidth : 0,//滚动对象子块宽度
			sHeight : 0,//滚动对象子块高度
			sCount : 0, //子块总计
			sColumn : 1, //显示列
			sRow : 1, //显示行
			sPage : 1, //当前页码
			sPageCount : 0, //总页码
			sAuto : false,//自动滚动
			sTime : 3000, //自动滚动时间间隔
			sTimer : null,
			sLoop : false, //循环
			sDebug : false, //测试输出
			isReset : false,
			returnClass : null,
			isNextBar : null,
			isDian : {}
		}		
		var opts = $.extend(_default, options);  
		opts.sBox = opts.sBox ? opts.sBox : $this;
		this.defaults = function(options){
			return opts = $.extend(opts, options);;
		};	
		this.LocatLeft = function(page){
			opts.sPage = page;
			__scrollLeft();
		}		
		if(opts.returnClass){
			returnClass = eval(opts.returnClass);
			returnClass(this);
		}
		opts.isPause = true;
		if(opts.aLeft){
			opts.aLeft.css({'cursor':'pointer'}).bind({
				click : function(){__scrollRight();}
			});
			if(opts.sAuto){opts.sTimer = setTimeout(__scrollLeft,opts.sTime);}
		}
		if(opts.aRight){
			opts.aRight.css({'cursor':'pointer'}).bind({
				click : function(){__scrollLeft();}
			});
		}
		if(opts.aUp){
			opts.aUp.css({'cursor':'pointer'}).bind({
				click : function(){__scrollDown();}
			});
			if(opts.sAuto){opts.sTimer = setTimeout(__scrollUp,opts.sTime);}			
		}
		if(opts.aDown){
			opts.aDown.css({'cursor':'pointer'}).bind({
				click : function(){__scrollUp();}
			});
		}
		__setAStyle();
		__ding();
		//__setLoop();
		return this;
		function __init(){	
			C.defaults(opts);
			var isReset = opts.isReset;
			if(isReset){
				opts.isReset = false;
			}
			var obj = opts.sChild.eq(0);
			//计算单个子模块的宽高
			if(!opts.scWidth){		
				var ml = __getCss(obj,'margin-left');
				var mr = __getCss(obj,'margin-right');
				var pl = __getCss(obj,'padding-left');
				var pr = __getCss(obj,'padding-right');
				var bl = __getCss(obj,'border-left-width');
				var br = __getCss(obj,'border-right-width');
				opts.scWidth = opts.sChild.eq(0).width() + ml + mr + pl + pr + bl + br;
				opts.sBox.css({'width':opts.scWidth * opts.sChild.length+'px'});
			}

			if(!opts.scHeight){
				var mt = __getCss(obj,'margin-top');
				var mb = __getCss(obj,'margin-bottom');
				var pt = __getCss(obj,'padding-top');
				var pb = __getCss(obj,'padding-bottom');
				var bt = __getCss(obj,'border-top-width');
				var bb = __getCss(obj,'border-bottom-width');
				opts.scHeight = opts.sChild.eq(0).height() + mt + mb + pt + pb + bt + bb;
				opts.sBox.css({'height':opts.scHeight * opts.sRow+'px'});
			}
			//计算子模块总数
			//if(isReset)
			if(!opts.sCount){
				opts.sCount = opts.sChild.length;
			}
			//计算总页码
			if(!opts.sPageCount || isReset){
				opts.sPageCount = Math.ceil(opts.sCount / (opts.sColumn * opts.sRow));
			}
			//判断是否自动播放
			if(opts.sAuto && opts.sPageCount != 1){
				opts.sBox.hover(function(){
					clearTimeout(opts.sTimer);
					opts.isPause = false;
				},function(){
					opts.isPause = true;
					if(opts.aLeft){
						opts.sTimer = setTimeout(__scrollLeft,opts.sTime);
					}else if(opts.aUp){
						opts.sTimer = setTimeout(__scrollUp,opts.sTime);
					}
				});
			}
		}
		
		function __setLoop(){
			if(!opts.sLoop){return;}
			var tagname = opts.sBox.attr("tagName");
			var classname = opts.sBox.attr("class");
			var style = opts.sBox.attr("style");
			var isNextBar = $("<"+tagname+"></"+tagname+">")
				.addClass(classname)
				.attr('style',style)
				.html(opts.sBox.html())
				.css({
					'left' : opts.sBox.width()+'px'
				});
			isNextBar.appendTo(opts.sBox.parent());
			opts.isNextBar = isNextBar;
		}

		function __getCss(obj,pre){
			if(!obj || obj.length == 0 || !obj.length)return 0;
			var cssPre = obj.css(pre);
			return cssPre.substring(0, cssPre.indexOf("px")) * 1;		
		}
		
		/*执行向左滚动*/
		function __scrollLeft(){
			__init();
			if(!opts.sBox.is(":animated")){
				clearTimeout(opts.sTimer);
				if(opts.sPageCount == opts.sPage){
					if(opts.sLoop){
						opts.sBox.animate({'left' :'0px' }, "slow");
						opts.sPage = 1;	
					}
				}else{
					opts.sBox.animate({'left' : -(opts.scWidth * opts.sColumn * opts.sPage)+ 'px' }, "slow"); 
					opts.sPage++;				
				}
				if(opts.sAuto && opts.isPause){opts.sTimer = setTimeout(__scrollLeft,opts.sTime);}
				__setdingClass(opts.sPage);
			}
			__setAStyle();
			var str = opts.sCount+','+opts.sPageCount+','+opts.sPage;
			__debug(str);
		}
		
		/*执行向右滚动*/
		function __scrollRight(){
			__init();
			if(!opts.sBox.is(":animated")){
				clearTimeout(opts.sTimer);
				if(opts.sPage == 1){
					if(opts.sLoop){
						opts.sBox.animate({'left' : '-='+opts.scWidth * opts.sColumn *(opts.sPageCount - 1) + 'px' }, "slow");
						opts.sPage = opts.sPageCount;
					}
				}else{
					var left = __getCss(opts.sBox,'left');
					//alert(left);
					left  = left + (opts.scWidth * opts.sColumn);
					opts.sBox.animate({'left' : left + 'px' }, "slow"); 
					opts.sPage--;			
				}
				if(opts.sAuto && opts.isPause){opts.sTimer = setTimeout(__scrollLeft,opts.sTime);}
				__setdingClass(opts.sPage);
			}
			__setAStyle();
			var str = 'sCount='+opts.sCount+',sPageCount='+opts.sPageCount+',sPage='+opts.sPage+',left='+left+','+opts.scWidth * opts.sColumn;
			__debug(str);
		}
		
		function __scrollUp(){
			__init();
			if(!opts.sBox.is(":animated")){
				clearTimeout(opts.sTimer);
				if(opts.sPageCount == opts.sPage){
					if(opts.sLoop){
						opts.sBox.animate({'top' :'0px' }, "slow");
						opts.sPage = 1;
					}
				}else{
					opts.sBox.animate({'top' : -(opts.scHeight * opts.sRow * opts.sPage)+ 'px' }, "slow"); 
					opts.sPage++;					
				}
				if(opts.sAuto && opts.isPause){opts.sTimer = setTimeout(__scrollUp,opts.sTime);}				
			}
			__setAStyle();
			var str = opts.sCount+','+opts.sPageCount+','+opts.sPage;
			__debug(str);		
		}
		function __scrollDown(){
			__init();
			if(!opts.sBox.is(":animated")){
				if(opts.sPage == 1){
					if(opts.sLoop){
						opts.sBox.animate({'top' : '-='+opts.scHeight * opts.sRow *(opts.sPageCount - 1) + 'px' }, "slow");
						opts.sPage = opts.sPageCount;
					}
				}else{
					opts.sBox.animate({'top' : '+='+opts.scHeight * opts.sRow + 'px' }, "slow"); 
					opts.sPage--;			
				}			
			}
			__setAStyle();
			var str = opts.sCount+','+opts.sPageCount+','+opts.sPage;
			__debug(str);		
		}
		
		function __ding(){
			__init();
			var dian = opts.isDian;
			var box = $(dian.box);
			if(box.length <= 0){return}
			var cn = dian.classname;
			var fcn = dian.focusclassname;
			var tagname = box.find("."+cn).attr("tagName");
			var html = '';
			for(var i = 0; i < opts.sPageCount; i++){
				html += '<'+tagname+' class="'+cn+'"></'+tagname+'>';
			}
			box.html(html);
			__dingChildClick();
			__setdingClass(1);
		}
		
		function __setdingClass(index){
			__init();
			var dian = opts.isDian;
			var box = $(dian.box);
			if(box.length <= 0){return}
			var cn = dian.classname;
			var fcn = dian.focusclassname;
			var tagname = box.find("."+cn).attr("tagName");			
			box.find(tagname).removeClass(fcn);
			box.find(tagname).eq(index - 1).addClass(fcn);
		}
		
		function __dingChildClick(){
			__init();
			var dian = opts.isDian;
			var box = $(dian.box);
			if(box.length <= 0){return}
			var cn = dian.classname;
			var fcn = dian.focusclassname;
			var tagname = box.find("."+cn).attr("tagName");	
			box.find(tagname).each(function(index){
				$(this).hover(function(){
					opts.isPause = false;
					opts.sPage = index;
					__scrollLeft();
					__pause();
					return false;
				},function(){
					opts.isPause = true;
					if(opts.sAuto){opts.sTimer = setTimeout(__scrollLeft,opts.sTime);}
				});
			});
		}
		
		function __pause(){
			clearTimeout(opts.sTimer);
			opts.isPause = true;
		}
		
		function __debug(str){
			if(!opts.sDebug){return;}
			var o = opts.sBox.offset();
			if($("#cScroll_debug").length > 0){
			}else{
				$('<div id="cScroll_debug" style="position:absolute;overflow:hidden;border:1px solid #999;background:#f5f5f5;padding:5px 20px;z-index:99999"></div>')
					.appendTo($("body"))
					.css({
						'left' : o.left - 50+'px',
						'top' : o.top - 50+'px'
					});
			}
			$("#cScroll_debug").html(str);		
		}
				
		function __setAStyle(){
			if(!opts.aStyle){return;}
			__init();
			var page = opts.sPage;
			var pagecount = opts.sPageCount;
			if(pagecount <= 1){
				if(opts.aLeft){opts.aLeft.addClass('spot_scroll_left_none').css({'cursor':'default'});}
				if(opts.aUp){opts.aUp.addClass('spot_scroll_left_none').css({'cursor':'default'});}				
				if(opts.aRight){opts.aRight.addClass('spot_scroll_right_none').css({'cursor':'default'});}
				if(opts.aDown){opts.aDown.addClass('spot_scroll_right_none').css({'cursor':'default'});}
				
			}else if(page == 1){
				if(opts.aLeft){opts.aLeft.addClass('spot_scroll_left_none').css({'cursor':'default'});}
				if(opts.aUp){opts.aUp.addClass('spot_scroll_left_none').css({'cursor':'default'});}	
				if(opts.aRight){opts.aRight.removeClass('spot_scroll_right_none').css({'cursor':'pointer'});}
				if(opts.aDown){opts.aDown.removeClass('spot_scroll_right_none').css({'cursor':'pointer'});}				
			}else if(page == pagecount){
				if(opts.aLeft){opts.aLeft.removeClass('spot_scroll_left_none').css({'cursor':'pointer'});}
				if(opts.aUp){opts.aUp.removeClass('spot_scroll_left_none').css({'cursor':'pointer'});}				
				if(opts.aRight){opts.aRight.addClass('spot_scroll_right_none').css({'cursor':'default'});}
				if(opts.aDown){opts.aDown.addClass('spot_scroll_right_none').css({'cursor':'default'});}				
			}else{
				if(opts.aLeft){opts.aLeft.removeClass('spot_scroll_left_none').css({'cursor':'pointer'});}
				if(opts.aUp){opts.aUp.removeClass('spot_scroll_left_none').css({'cursor':'pointer'});}				
				if(opts.aRight){opts.aRight.removeClass('spot_scroll_right_none').css({'cursor':'pointer'});}
				if(opts.aDown){opts.aDown.removeClass('spot_scroll_right_none').css({'cursor':'pointer'});}					
			}

		}
	};


	//重置
	$.fn.cScroll.setReset = function(cs){
		cs = cs || {};
		$.fn.cScroll.defaults = $.extend($.fn.cScroll.defaults,cs);
		if($.fn.cScroll.defaults.aLeft || $.fn.cScroll.defaults.aRight){
			$.fn.cScroll.defaults.sBox.animate({'left' :  '0px' }, "slow");
		}else if($.fn.cScroll.defaults.aUp || $.fn.cScroll.defaults.aDown ){
			$.fn.cScroll.defaults.sBox.animate({'top' :  '0px' }, "slow");
		}
		$.fn.cScroll.defaults.sPage = 1;
		$.fn.cScroll.defaults.isReset = true;
	}
})(jQuery);  