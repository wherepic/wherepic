var C = {
    status : {err : -1,warn : 0,info : 1, succ : 100}
};
jQuery(function(){
	$.fn.extend({
		_submitForm : function(before,callback,noreset){
			var $this = $(this);
			noreset=noreset?noreset:false;
			var action = isAjaxPost($this.get(0).action);
			$this.submit(function(){
				var submit_btn = $this.find(":submit").eq(0);
				var submit_val = submit_btn.val() ? submit_btn.val() : submit_btn.attr("action-value");
				submit_btn.val(S.lang.ajaxPost).addClass("button_cancel");
				var status = $this.attr("poststatus");
				if(status){return false;}else{
					$this.attr("poststatus",true);
				}
				if(before && typeof(before) == 'string'){
					before = toFunc(before);
				}						
				if ($.isFunction(before)){
					if(!before()){
						submit_btn.val(submit_val).removeClass("button_cancel");
						$this.removeAttr("poststatus");
						return false;
					}
				}
				$.ajax({
					type: 'POST',
					url:action,
					data:$this.serialize(),
					dataType: 'JSON',	
					success: function(result){
						
						$this.removeAttr("poststatus");
						if(noreset==false){
							$this.get(0).reset();
						}
						if(callback && typeof(callback) == 'string'){
							callback = toFunc(callback)
						}
						if(typeof(result) == 'string'){
							result = toFunc(result);
						}	
						try{
						if(result.callback){
							rcall = toFunc(result.callback);
							rcall();
						}
						}catch(e){};
						submit_btn.val(submit_val).removeClass("button_cancel");
						if ($.isFunction(callback)) callback(result,$this);
					}
				});
				return false;				
			});
		},		
		_vailInt:function(){		
			$(this).bind("input",function(){
				var v = $(this).val();
				v = v.replace(/\D/g,'');
				$(this).val(v);
			}).bind("keyup",function(){
				var v = $(this).val();
				v = v.replace(/\D/g,'');
				$(this).val(v);
			});	
		},
		_vailNumber:function(){		
			$(this).bind("input",function(){
				var v = $(this).val();
				v = v.replace(/[^\d.]/g,"");
				v = v.replace(/^\./g,"");
				v = v.replace(/\.{2,}/g,".");
				v = v.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
				$(this).val(v);
	
			}).bind("keyup",function(){
				var v = $(this).val();
				v = v.replace(/[^\d.]/g,"");
				v = v.replace(/^\./g,"");
				v = v.replace(/\.{2,}/g,".");
				v = v.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
				$(this).val(v);
				
			});		
		},
		_maxLength : function(max,_target,split){
			var _s =  $(this);		
			var split = (split == 'undefined' || split == '' || split == null) ? ' | ' : split;
			var _t =typeof(_target)  == 'object' ? _target : $("#"+_target);
			var len = 0;
			if(_s.val()){
				len = _s.val().length;
			}
			if(_t){
				_t.html(len+split+max).css({'color':'#888','font-family': 'Arial','padding':'2px'});
			}
			_s.bind("input",function(){
				var l = _s.val().length;
				if(l > max){
					_s.val(_s.val().substr(0,max));
				}
				if(_t)_t.html(l+split+max);			
			}).bind("keyup",function(){
				var l = _s.val().length;
				if(l > max){
					_s.val(_s.val().substr(0,max));
				}
				if(_t)_t.html(l+split+max);				
			});
		},
		_upload : function(params){
			var _this = $(this);
			var settings = $.extend({
				'swf': S.flashPath+'/uploadify.swf',
				'uploader': '',
				'scriptAccess' : 'always', //sameDomain  
				'width' : '70',
				'height' : '24',
				'buttonText' : S.lang.selectFile,
				'multi' : true,
				'auto' : true,
				'fileTypeExts' : '*.*',
				'fileSizeLimit' : '10MB',
				'uploadLimit' : 10, //同时上传的文件数
				'onInit' : function(){},//初始化
				'onSelect' : function(event,queueID,fileObj){},
				'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal){},//上传进度
				'onUploadStart' : function(file){},		
				'onUploadSuccess' : function(file, data, response){},		
				'onUploadError' : function(file, errorCode, errorMsg, errorString){},		
				'onUploadComplete' : function(file){},//文件上传完成后触发
				'onFallback' : function(){alert("'Flash was not detected.")},
				/*
				onQueueComplete[queueData]
				uploadsSuccessful :上传的所有文件个数。 
				uploadsErrored ：出现错误的个数。		
				*/					
				'onQueueComplete' : function(queueData){}//所有的文件上传完成后触发
			},params);
			_this.uploadify(settings);
		},
        _bindInputFocus : function(){
            $(this).each(function(){
                var _this = $(this);
                var de_value = _this.attr("action-value");
                if(!de_value){
                    de_value = _this.val();
                    $(this).attr("action-value",de_value);
                }else{
                    _this.val(de_value);
                }
                var type = (_this.attr("type")).toLowerCase();
                if(type == 'password'){
                    var input = $('<input type="text" name="'+_this.attr("name")+'_text" id="'+_this.attr("id")+'_text" value="'+de_value+'" action-value="'+de_value+'" class="'+(_this.attr("class") ? _this.attr("class") : '')+'" action-show="#'+_this.attr("id")+'"/>');
                    _this.before(input);
                    _this.hide(0);
                    _this.val('');
                    _this.attr("action-show",'#'+_this.attr("id")+'_text');
                    input.focus(function(){
                        $(this).hide();
                        $($(this).attr("action-show")).show().focus();                        
                    })
                }
                
                _this.unbind('focus').focus(function(){
                    if(de_value == _this.val()){
                        $(this).val('') ;  
                    }                    
                }).unbind('blur').blur(function(){
                    var _this = $(this);
                    if(type == 'password'){
                        if(_this.val() == ''){
                            _this.hide();
                            $(_this.attr("action-show")).show(); 
                        }
                    }else if(_this.val() == ''){
                         _this.val(de_value) ;
                    }                                
                });
            });
        }	

	});

    $("input[type=text],input[type=password],input[type=file],textarea").focus(function(){
        $(this).addClass("input_focus");
    }).blur(function(){
        $(this).removeClass("input_focus");
    });	
});

function getCss(obj,pre){
	if(!obj || obj.length == 0 || !obj.length)return 0;
	var cssPre = obj.css(pre);
	if(!cssPre){return 0;}
	return cssPre.substring(0, cssPre.indexOf("px")) * 1;		
}

function getScroll(){
	var scrollTop = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
	var scrollHeight = document.documentElement.scrollHeight ? document.documentElement.scrollHeight : document.body.scrollHeight;
	var clientHeight = document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;	
	var clientWidth = document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.clientWidth;	
	
	return [scrollTop,scrollHeight,clientHeight,clientWidth];
}
function windowScroll(){
	scrollTop();
	$(window).scroll(function(){
		var client = getScroll();
		if(client[0] >= client[2] - 300){
			$("#scrollTop").fadeIn('1000');
		}else{
			$("#scrollTop").fadeOut('1000');
		}
	});
} 
function scrollTop(){
	$("#scrollTop").click(function(){
		$(document).scrollTop(0); 
	});
}
function isAjaxPost(url){
	if(url.indexOf("?") != -1){
		return url+'&isAjax=1';
	}else{
		return url+'?isAjax=1';
	}
}
function isVali(){
	//整数检查
	$("input[vali=isInt]").each(function(){
		$(this)._vailInt();
	});
	//数字检查
	$("input[vali=isNumber]").each(function(){
		$(this)._vailNumber();
	});	
}