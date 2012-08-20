(function($){
	$.fn.vaildator = function(opt){
		var options = $.extend({
			pop : false,
			wideword : true,
			min : 0,
			max : 0,
			isEmpty : false,
			type : "size",
			onerror:"",
			defaultval : '',
			validatetype:"inputValid",
            focusObj : null		
		},opt);
		var $this = $(this);
		var elem = $this.get(0);
		var val = $this.val();
		var sType = elem.type;	
		var flag = false;	
		if(options.onerror == ''){
			options.onerror = 'The '+$this.attr("name")+' can not be empty!';
		}
        if(options.defaultval == ''){
            options.defaultval = $(this).attr("action-value");            
        }
		if(options.validatetype){
			flag = inputValid();
			if(!flag){
				$this.addClass("input_err").focus(function(){$(this).removeClass("input_err")});
				if(options.pop){
					$this.pop({content:options.onerror,cat:'error'});
                    scroll();
				}else{
                    Tips(options.onerror);
                }	
									
			}else{
				$this.addClass("input_succ").focus(function(){$(this).removeClass("input_succ")});
                $('#isTipsBox'+$this.attr("id")).remove();
			}
			return flag;
		}
		function inputValid(){
			switch(sType){
				case "text":
				case "hidden":
				case "password":
				case "textarea":
				case "file":
                    val = Utils.trim(val);
					if(Utils.isEmpty(val) && !options.isEmpty){
						return false;
					}
					if(val == options.defaultval){
						return false;
					}
					
					var length = getLength();

					if(options.min > 0 && length < options.min){
						return false;
					}
					if(options.max > 0 && length > options.max){
						return false;
					}	
					return true;					
			}	
		}
   		function getLength(){
			var elem = $this.get(0);
	        sType = elem.type;
	        var len = 0;
	        switch(sType){
				case "text":
				case "hidden":
				case "password":
				case "textarea":
				case "file":
			        var val = $this.val();
					if (options.wideword){
						for (var i = 0; i < val.length; i++){
							if (val.charCodeAt(i) >= 0x4e00 && val.charCodeAt(i) <= 0x9fa5){ 
								len += 2;
							}else {
								len++;
							}
						}
					}
					else{
						len = val.length;
					}
			        break;
				case "checkbox":
				case "radio": 
					len = $("input[@type='"+sType+"'][@name='"+$this.attr("name")+"'][@checked]").length;
					break;
			    case "select-one":
			        len = elem.options ? elem.options.selectedIndex : -1;
					break;
				case "select-multiple":
					len = $("select[@name="+elem.name+"] option:selected").length;
					break;
		    }
			return len;
   		}

   		function scroll(){
   			var top = 0;
   			if(!options.pop){
   				//var pos = $("#main_block").offset();
   				var pos = $this.offset();
   				top = pos.top - 50;
   			}else{
   				var pos = $this.offset();
   			}
   			
   			top = top > 0 ? top : 0;
			var client = getScroll();
			if(client[0] >= client[2] - 300){
				$(document).scrollTop(top);
			} 			
   		}
        
        function Tips(message){
            var _id = 'isTipsBox'+$this.attr("id");
            if($('#'+_id).length > 0){
                return false;
            }
            var div = $('<div class="isTipsBox" id="'+_id+'"><div class="arr"><span>◆</span></div><div class="tb_background">'+message+'</div> </div>');
            var pos = $this.position();
            var height = $this.innerHeight(true);
            var width = $this.innerWidth(true);
            if(options.focusObj){
                pos = options.focusObj.position();    
                height = options.focusObj.innerHeight(true);    
                width = options.focusObj.innerWidth(true);    
            }
            
            div.css({
                'top' : pos.top + height + div.find("em").innerHeight(true) + 4 +'px',
                'left' : pos.left+'px',
                'width' : width +'px'
            }).appendTo($this.parent());
            $this.focus(function(){
                div.fadeOut('fast',function(){
                    $(this).remove();
                })
            })    
        }

	};
})(jQuery);