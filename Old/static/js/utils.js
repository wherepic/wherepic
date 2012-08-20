var Utils = new Object();
Utils.htmlEncode = function(text){
  return text.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

Utils.trim = function( text ){
  if (typeof(text) == "string"){
	return text.replace(/^\s*|\s*$/g, "");
  }
  else{    
	  return text;
  }
}

Utils.isEmpty = function(val){
  switch (typeof(val)){
	case 'string':
	  return Utils.trim(val).length == 0 ? true : false;
	  break;
	case 'number':
	  return val == 0;
	  break;
	case 'object':
	  return val == null;
	  break;
	case 'array':
	  return val.length == 0;
	  break;
	default:
	  return true;
  }
}

Utils.isNumber = function(val){
  var reg = /^\-?\d+\.?\d*$/;
  return reg.test(val);
}

Utils.isInt = function(val){
  if (val == ""){
	return false;
  }
  var reg = /\D+/;
  return !reg.test(val);
}

Utils.isEmail = function(email){
  //var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
  var reg1 =  /^([a-zA-Z0-9]+[_|\_|\.]?)+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;;
  return reg1.test( email );
}

Utils.fixEvent = function(e){
  var evt = (typeof e == "undefined") ? window.event : e;
  return evt;
}

Utils.srcElement = function(e){
  if (typeof e == "undefined") e = window.event;
  var src = document.all ? e.srcElement : e.target;
  return src;
}

Utils.isTime = function(val){
  var reg = /^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/;
  return reg.test(val);
}

Utils.Phone = function(val){
	var reg = /^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/;
	return reg.test(val);
}

Utils.Mobile = function(val){
	var reg = /^((\(\d{3}\))|(\d{3}\-))?(13|15|18)\d{9}$/;
	return reg.test(val);
}

Utils.Chinese = function(val){
	var reg = /^[\u0391-\uFFE5]+$/;
	return reg.test(val);
}
//包含中文
Utils.inChinese = function(val){
	var reg = /[\u0391-\uFFE5]+/g;
	return reg.test(val);
}

Utils.English = function(val){
	var reg = /^[A-Za-z]+$/;
	return reg.test(val);
}
Utils.isQQ = function(val){
	return (/^[1-9]\d{4,}$/.test(val));  
}
Utils.isURL = function(val){
	return (/^(http|https):\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(val));    
}
Utils.UserName = function(val){
	var reg = /^[A-Za-z0-9\u0391-\uFFE5]+$/ig;
	return reg.test(val);
}
function getLen(str){ 
   var strlength=0; 
   for (i=0;i<str.length;i++){ 
     if (Utils.Chinese(str.charAt(i))==true) 
        strlength=strlength + 2; 
     else 
        strlength=strlength + 1; 
   } 
	return strlength; 
} 