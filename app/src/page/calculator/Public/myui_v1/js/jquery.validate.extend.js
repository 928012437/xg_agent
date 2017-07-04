jQuery.extend(jQuery.validator.messages, {
        required: "必填项",
		remote: "请修正该字段",
		email: "请输入正确格式的电子邮件，如'yourname@11186.com'",
		url: "请输入正确的网址，如'http://www.11186.com'",
		date: "请输入正确的日期",
		dateISO: "请输入正确的日期 (ISO).",
		number: "请输入正确的数字",
		digits: "只能输入整数",
		creditcard: "请输入正确的信用卡号",
		equalTo: "请再次输入相同的内容",
		accept: "请输入拥有正确后缀名的字符串",
		maxlength: jQuery.validator.format("最多可输入 {0} 字"),
		minlength: jQuery.validator.format("至少要输入 {0} 字"),
		rangelength: jQuery.validator.format("至少要输入 {0} 字,不超过 {1} 字"),
		range: jQuery.validator.format("请输入一个介于 {0} 到 {1} 之间的值"),
		max: jQuery.validator.format("最多可输入 {0} 字"),
		min: jQuery.validator.format("至少要输入 {0} 字"),
		downListNew: "请选择一项！"
});

$(document).ready(function(){
// 中文字两个字节 
jQuery.validator.addMethod("byteRangeLength", function(value, element, param) { 
  var length = value.length; 
  for(var i = 0; i < value.length; i++){ 
   if(value.charCodeAt(i) > 127){ 
    length++; 
   } 
  } 
  return this.optional(element) || ( length >= param[0] && length <= param[1] ); 
}, "请确保输入的值在3-15个字节之间(一个中文字算2个字节)"); 

/* 追加自定义验证方法 */ 
// 身份证号码验证 
jQuery.validator.addMethod("isIdCardNo", function(value, element) { 
	var tel = /^\d{15}$|^\d{18}$|^\d{17}[xX]$/;
	return this.optional(element) || (tel.test(value)); 
}, "请正确输入您的身份证号码"); 

// 非特殊字符输入验证 
jQuery.validator.addMethod("isNotSpecial", function(value, element) { 
  return this.optional(element) || /^[a-zA-Z0-9\\u4e00-\u9fa5]+$/.test(value); 
}, "只能包括中文字、英文字母"); 

jQuery.validator.addMethod("isOrgName", function(value, element) { 
	return true;
	  //return this.optional(element) || /^([a-zA-Z0-9]|[\_\@\&\%]|[\u0391-\uFFE5])+$/.test(value); 
}, "只能包括中文字、英文字母、数字和 _ @ & %"); 

jQuery.validator.addMethod("isCertiNum", function(value, element) { 
	  return this.optional(element) || /^([a-zA-Z0-9]|[\_\@\&\%\-]|[\u0391-\uFFE5])+$/.test(value); 
}, "只能包括中文字、英文字母、数字和 _ - @ & %"); 

//字符验证 
jQuery.validator.addMethod("username", function(value, element) { 
  return this.optional(element) || /^[\u0391-\uFFE5\w]+$/.test(value); 
}, "用户名只能包括中文字、英文字母、数字和下划线"); 

//年份验证 
jQuery.validator.addMethod("isYear", function(value, element) { 
	  var tel = /^(((1[7-9])+\d{2})|(200[0-9])|(201[0-1]))$/; 
	  return this.optional(element) || (tel.test(value)); 
}, "请正确填写年份"); 
  
//月份验证 
jQuery.validator.addMethod("isMonth", function(value, element) { 
	  var tel = /^(([1-9]{1})|(1[0-2]{1}))$/; 
	  return this.optional(element) || (tel.test(value)); 
}, "请正确填写月份"); 

// 手机号码验证 
jQuery.validator.addMethod("isMobile", function(value, element) { 
  var length = value.length; 
  return this.optional(element) || (length == 11 && /^((13|15|18)+\d{9})$/.test(value)); 
}, "请正确填写您的手机号码，长度为11位"); 

//电话号码验证 
jQuery.validator.addMethod("isPhone", function(value, element) { 
  var tel = /^(?!0)+\d{7,8}$/;
  return this.optional(element) || (tel.test(value)); 
}, "请正确填写您的电话号码"); 

// 电话号码区号验证 
jQuery.validator.addMethod("isCode", function(value, element) { 
  var tel = /^(0)+\d{2,3}$/; 
  return this.optional(element) || (tel.test(value)); 
}, "请正确填写您的电话号码区号"); 

//完整电话号码验证 
jQuery.validator.addMethod("isFullPhone", function(value, element) { 
var tel = /^(0[0-9]{2,3}\-)?([1-9][0-9]{6,7})+(\-[0-9]{1,4})?$/; 
return this.optional(element) || (tel.test(value)); 
}, "电话号码格式错误"); 

// 邮政编码验证 
jQuery.validator.addMethod("isZipCode", function(value, element) { 
  var tel = /^[0-9]{6}$/; 
  return this.optional(element) || (tel.test(value)); 
}, "请正确填写您的邮政编码"); 

//下拉菜单验证非"param"
$.validator.addMethod("downList",function(value, element, param) {
	return value != 0;
},"请选择一项！");

//QQ号码验证 
jQuery.validator.addMethod("qq", function(value, element) { 
var tel = /^[1-9]\d{4,10}$/; 
return this.optional(element) || (tel.test(value)); 
}, "qq号码格式错误,长度为5~11位");

//msn号码验证 
jQuery.validator.addMethod("msn", function(value, element) { 
	var tel = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
	//var tel = /^((([a-zA-Z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-zA-Z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/; 
return this.optional(element) || (tel.test(value)); 
}, "MSN格式错误");

//阿里旺旺号码验证 
jQuery.validator.addMethod("wangwang", function(value, element) { 
var tel = /^[1-9]\d{4,10}$/; 
return this.optional(element) || (tel.test(value)); 
}, "阿里旺旺号码格式错误");

//纯数字验证
jQuery.validator.addMethod("isNumberOnly", function(value, element) { 
  var num = /^\d+$/; 
  return this.optional(element) || (num.test(value)); 
}, "请填写数字！"); 

//百分比验证
jQuery.validator.addMethod("isPercent", function(value, element) { 
  var num = /^\d+(\.?\d{0,1})?$/; 
  return this.optional(element) || (num.test(value)); 
}, "请正确填写百分数字,小数点后最多一位！"); 

//有效期验证 
jQuery.validator.addMethod("isIndate", function(value, element) { 
  var tel = /^(?!0)+\d{1,2}$/;
  return this.optional(element) || (tel.test(value)); 
}, "请正确填写有效期"); 

//资金金额验证
jQuery.validator.addMethod("isFund", function(value, element) { 
  var num = /^(?!0)+\d{1,7}$/; 
  return this.optional(element) || (num.test(value)); 
}, "请正确填写整数金额，最多为7位！"); 

//园区/企业数量验证
jQuery.validator.addMethod("isParkCount", function(value, element) { 
	var num=/^(?!0)+\d{1,11}$/;
	  return this.optional(element) || (num.test(value)); 
}, "请输入大于零的整数，最多为11位！");

//只允许一位小数验证
jQuery.validator.addMethod("isDecimals", function(value, element) { 
  var num = /^\d+(\.?\d{0,1})?$/; 
  return this.optional(element) || (num.test(value)); 
}, "请正确输入数字，小数点后最多一位！"); 

//只允许两位小数验证
jQuery.validator.addMethod("isDecimals2", function(value, element) { 
  var num = /^\d+(\.?\d{0,2})?$/; 
  return this.optional(element) || (num.test(value)); 
}, "请正确输入数字，小数点后最多两位！"); 

jQuery.validator.addMethod("isLandName", function(value, element) { 
	   return this.optional(element) || /^([a-zA-Z0-9]|[\(\)]|[\u0391-\uFFE5])+$/.test(value); 
}, "只能包括中文字、英文字母、数字和( )"); 

jQuery.validator.addMethod("isCetiNum", function(value, element) { 
	   return this.optional(element) || /^([a-zA-Z0-9]|[\.])+$/.test(value); 
}, "只能包括英文字母、数字和 . "); 

jQuery.validator.addMethod("isNumAndDot", function(value, element) { 
	   return this.optional(element) || /^([0-9]|[\.])+$/.test(value); 
}, "只能包括数字和小数点！"); 

jQuery.validator.addMethod("isLetterNum", function(value, element) { 
	   return this.optional(element) || /^([a-zA-Z0-9])+$/.test(value); 
}, "只能包括英文字母和数字"); 
});