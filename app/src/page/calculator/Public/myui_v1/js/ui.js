 document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        // // 通过下面这个API隐藏右上角按钮
        // WeixinJSBridge.call('hideOptionMenu');
        // WeixinJSBridge.call('hideToolbar'); 
    });

 function initDateControl(dom){
     var userAgent = navigator.userAgent.toLowerCase();
     if (GetIsNewWxVersion(userAgent) && userAgent.indexOf("mac") < 0) {
         var currentYear = (new Date()).getFullYear();
         var defaultSettings = {
             dateFormat: "yy-mm-dd",
             dateOrder: "yymmdd",
             display: "modal",
             lang: "zh",
             mode: "scroller",
             theme: "android",
             showNow: true,
             startYear: currentYear - 10,
             endYear: currentYear + 10
         };
         dom.mobiscroll().date(defaultSettings);
     }
 }
var handle = function (e) {
    e.preventDefault();
};

// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符， 
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) 
// 例子： 
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423 
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18 
Date.prototype.Format = function (fmt) { //author: meizz
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};

Date.prototype.addDays = Date.prototype.addDays || function (days) {
    this.setDate(this.getDate() + days);
    return this;
};

//获得指定范围的随机数
function randomByMM(min, max) {
    return min + Math.round(Math.random() * (max - min));
};

UI = {};
var tmpinterval=setInterval(function(){
 UI.windowHeight = $(window).height();
 if(UI.windowHeight!=0){
     clearInterval(tmpinterval);
 }
},100);
UI.formErrorMsg = {
    send: function (id, msg,formid) {
        var ctrl = $("#" + id);
        if(formid){
            ctrl = $("#"+formid+" #"+id);
        }
        var warpper = ctrl.closest(".form-group");
        UI.formErrorMsg.remove(id);
        warpper.after('<div style="display:block" class="error" for="' + id + '">' + msg + '</div>');
    },
    remove: function (id,formid) {
        if(formid){
            if ($("#"+formid+" div.error[for='" + id + "']").length > 0) {
                $("#"+formid+" div.error[for='" + id + "']").remove();
            }
        }

        if ($("div.error[for='" + id + "']").length > 0) {
            $("div.error[for='" + id + "']").remove();
        }
    }
}
//页面自动滚动
function scrollTopAuto(obj, topNum, speed) {
    var obj = obj || document;
    var topNum = topNum || 0;
    var speed = speed || 500;
    $(obj.documentElement).animate({scrollTop: topNum}, speed);//火狐
    $(obj.body).animate({scrollTop: topNum}, speed);//针对谷歌
}
//创建popWin_mask
UI.creatPopWin_mask = {
    creat: function (id, background, opacity, zIndex, formID) {
        UI.creatPopWin_mask.remove(id);
		//UI.creatPopWin_mask.fixedBtns();
        var newMask = $('<div class="popWin_mask"><div>&nbsp;&nbsp;</div></div>');
        newMask.attr("id", id);
        if (background) {
            newMask.css("background", background);
        }
        if (opacity) {
            newMask.css("opacity", opacity);
        }
        if (zIndex) {
            newMask.css("z-index", zIndex);
        }
        $("body").append(newMask);
        newMask.on("click", function () {
            var id = $(this).attr("id");
            UI.creatPopWin_mask.remove(id);
			var ulSelect = $("#mask").children(":eq(0)");
			var cName = ulSelect.attr("for");
			if(ulSelect.hasClass("MultiLayerSelect")){
				cName = ulSelect.find(".MultiLayerSelect_left").attr("for");
			}
			if(cName!=""){
                var fc;
                if(formID){
                    fc = $("#"+formID).find("[fc_for="+cName+"]");
                }
				else{
                    fc = $("[fc_for="+cName+"]");
                }
				ulSelect.appendTo(fc);
			}
            $("#mask").html("").hide();
            $("body").css("overflow", "auto");
            document.removeEventListener("touchmove", handle, false);
        });
		$("body").css("overflow", "hidden");
        newMask.css("height",UI.windowHeight).show();
//        newMask.css("height",UI.windowHeight).show();
        newMask.css({'z-index':99998});
    },
    remove: function (id) {
        var ids = id;
        if ($('#' + ids).length > 0) {
            $('#' + ids).remove();
        }
		
    },
	fixedBtns: function(){
		var fixedBtn = $(".fixedBottom:last ul");
		//var fixedBtnUL = fixedBtn.find("ul");
		var navigation_foot_top = UI.windowHeight - fixedBtn.height() ;
		//alert(navigation_foot_top);
		fixedBtn.css({"top":navigation_foot_top});
	}
}
//创建creatPopMsg
UI.creatPopMsg = {//typeClass:success,error
    creat: function (msg, id, typeClass) {
		var ids;
		if(id){
			ids = "msg_"+id;
		}else{
			ids = "msg_" + randomByMM(1, 10000);
		}
        UI.creatPopMsg.remove(ids);
        var newMsg = $('<div class="popMsgLayer"><a class='+ typeClass +'>' + msg + '</a></div>');
        newMsg.attr("id", ids);
        $("body").append(newMsg);
        setTimeout(function () {
            UI.creatPopMsg.remove(ids);
        }, 1500);
    },
    remove: function (id) {
        var ids = id;
        if ($('#' + ids).length > 0) {
            $('#' + ids).fadeOut(function () {
                $(this).remove();
            });
        }
    }
}
//初始化表单值
UI.formatForm = {
    init: function (id) {
        var form = $("#" + id);
        form.find("ul.checkbox").each(function (i, control) {
            var me = $(control);
            var cName = me.attr("for");
            var value = me.attr("value");
            if (value != "") {
                me.find('li[value="' + value + '"]').addClass('select').siblings().removeClass("select");
            } else {
                me.find('li').removeClass("select");
            }
            form.find("#" + cName).val(value);
        });
        form.find("ul.singleSelect").each(function (i, control) {
            var me = $(control);
            var cName = me.attr("for");
            var value = me.attr("value"), text;
            me.find("li").removeClass("select");
            var selectLi = me.find('li[value="' + value + '"]');
            var text = selectLi.text() == "" ? "请选择" : selectLi.text();
            selectLi.addClass('select').siblings().removeClass("select");
            form.find("#form_text_" + cName).text(text);
            form.find("#" + cName).val(value);
        });
        form.find("ul.multSelect").each(function (i, control) {
            var me = $(control);
            var cName = me.attr("for");
            var value = me.attr("value");
            me.find("li").removeClass("select");
            form.find("#" + cName).val(value);
            var text = [];
            if (value != "") {
                var values = value.split(",");
                $.each(values, function (i, val) {
                    var selectLi = me.find('li[value="' + val + '"]');
                    selectLi.addClass('select');
                    text.push(selectLi.text());
                });
            } else {
                text = ["请选择"];
            }
            form.find("#form_text_" + cName).html(text.join("、"));
        });

        form.find(".MultiLayerSelect").each(function (i, control) {
            var me = $(control);
            
            var me_left = me.find(".MultiLayerSelect_left");
            var me_right = me.find(".MultiLayerSelect_right");
            var cleftName = me_left.attr("for");
            var vleftValue = me_left.attr("value");
            var crightName = me_right.attr("for");
            var vrightValue = me_right.attr("value");
            var selectleftLi = me_left.find('li[value="' + vleftValue + '"]');
            var selectrightLi = me_right.find('ul li[value="' + vrightValue + '"]');
            form.find("#" + cleftName).val("");
            form.find("#" + crightName).val("");
            selectleftLi.removeClass('select');
            selectrightLi.removeClass('select');
            form.find("#form_text_"+cleftName).text("请选择");            
        });
    },
    clear: function (id) {
        var form = $("#" + id);
        UI.formatForm.init(id);
    }
}

;(function(){//封装on并增加tap方法，修复点击时的闪屏感提升响应速度
     var isTouch = ('ontouchstart' in document.documentElement) ? 'touchstart' : 'click';
     if(!$.fn.tap){
         $.fn.tap = function(){
             arguments[0] = (arguments[0] === 'click') ? isTouch : arguments[0];
             return $.fn.on.apply(this,arguments);
         }
     }
 })();

 //按钮active效果
 UI.active = function(param){
     for(var i = 0;i < param.length; i++){
         param[i].addEventListener('touchstart',function(e){
             e.target.style.opacity = '.75';
         });
         param[i].addEventListener('touchend',function(e){
             e.target.style.opacity = '1';
         })
     }
 }
$(function () {

    //初始化checkbox
    $("body").append('<div id="mask"></div><div id="msg"></div>');
    var mask = $("#mask");
    var initInterval=setInterval(function(){
        UI.windowHeight=$(window).height();
        if(UI.windowHeight!=0){
            var maskTop = (UI.windowHeight - mask.height())/2 + 50;
            mask.css("top",maskTop);
            clearInterval(initInterval);
        }
    },100);
	//var navigation_foot_top = UI.windowHeight - $(".fixedBottom>ul").height();
	//$(".fixedBottom>ul").css("top",navigation_foot_top);
    var current = null;
//    $(".container").click(function (event) {
//		if($("#mask").is(":hidden"))return false;
//        if (event.target.id != "form_text_" + current) {
//            var button = $("#mask").find('button').click();
//        }
//    });

    $("ul.checkbox").each(function (i, control) {
        var me = $(control);
        var cName = me.attr("for");
        var value = me.attr("value");
        var tableName = me.attr("tableName");
		var formID = me.parents("form").attr("id");
		
        var input = $("<input type='hidden' name='" + cName + "' id='" + cName + "' value='" + value + "' tableName='" + tableName + "'/>");
        input.insertBefore(me);
        var chlids = me.children();
        chlids.each(function (index, el) {
            el = $(el);
            el.html("<span>&nbsp;</span><div>" + el.html() + "</div>")
        });
        me.find('li[value="' + value + '"]').addClass('select');

        var doItemClick=true;
        me.find('li').bind('touchstart', function (e) {
            var select = $(this);
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    $('input').blur();
                    window.scrollTo(0,me.offset().top);
                    me.find("li.select").removeClass('select');
                    select.addClass('select');
                    input.val(select.attr("value"));
                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove', function () {
            doItemClick = false;
        });
    });


    $("ul.singleSelect").each(function (i, control) {
        var me = $(control);
        me.parent().parent().addClass('hasfocus');
        var cName = me.attr("for");
        var value = me.attr("value")||"";
        var tableName = me.attr("tableName")||"";
        var childLi = me.html();
        me.html('<div>' + childLi + '</div>');
		var formID = me.parents("form").attr("id");

        var selectLi = me.find('li[value="' + value + '"]');
        selectLi.addClass('select');
        var text = selectLi.text();

        var fc = me.parent();
		fc.attr("fc_for",cName);

        var input = $("<i class='arrow_right'></i><input type='hidden' name='" + cName + "' id='" + cName + "' value='" + value + "' tableName ='" + tableName + "'/><div class='select-value' id='form_text_" + cName + "'>" + (text == "" ? "请选择" : text) + "</div>");
        input.insertBefore(me);
//        var doItemClick = true;
//        me.find('li').bind('touchstart', function (e) {
//            var select = $(this);
//            doItemClick = true;
//            setTimeout(function () {
//                if (doItemClick) {
//                    if (!select.hasClass('select')) {
//                        me.find("li.select").removeClass('select');
//                        select.addClass('select');
//                        if(me.parents("form").find("div[for='"+cName+"']"))
//                        {
//                            UI.formErrorMsg.remove(cName);
//                        }
//                    }
//
//                    $("#mask").find('button').click();
//                    doItemClick = true;
//                }
//            }, 500);
//        }).bind('touchmove', function () {
//            doItemClick = false;
//        });


        me.find('li').unbind('tap').bind('tap', function (e) {
            var select = $(this);
            if (!select.hasClass('select')) {
                me.find("li.select").removeClass('select');
                select.addClass('select');
                if(me.parents("form").find("div[for='"+cName+"']"))
                {
                    UI.formErrorMsg.remove(cName);
                }
            }

            $("#mask").find('button').click();
        });

        fc.bind('touchstart', function (e) {
            var select = $(this);
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    $('input').blur();
                    window.scrollTo(0,me.offset().top);
                    $("#mask").find('button').click();
                    current = cName;
                    select.find("ul.singleSelect").appendTo("#mask");
                    var button = $('<button class="btn btn-block" style="display:none">确 定</button>');
                    button.click(function (event) {

                        //if($("#mask").is(":hidden"))return false;
                        var select = me.find("li.select");
                        if (select.length > 0) {
                            fc.find("#" + cName).val(select.attr("value"));
                            fc.find("#form_text_" + cName).html(select.text());
                        }
                        else {
                            fc.find("#" + cName).val("");
                            fc.find("#form_text_" + cName).html("请选择");
                        }
                        me.appendTo(fc);
                        mask.html("").hide();
                        $("#popWin_mask_" + cName).css({opacity:0});
                        setTimeout(function(){
                            UI.creatPopWin_mask.remove("popWin_mask_" + cName);
                            current = null;
                            $("body").css("overflow", "auto");
                            document.removeEventListener("touchmove", handle, false);
                        },500);

                        //console.log($("#form_text_" + cName));
                    });
                    UI.creatPopWin_mask.creat("popWin_mask_" + cName, null, null, 10003 ,formID);
                    mask.css("z-index", 99999).append(button).show();
                    $("body").css("overflow", "hidden");
                    document.addEventListener("touchmove", handle, false);
                    var myScroll = me.data("myScroll");
                    if (typeof myScroll == "undefined") {
                        var index_select = 0;
                        if(me.find("li").length > 3)
                        {
                            //计算定位
                            index_select = me.find("li.select").index();
                            if (index_select >=3){
                                index_select = index_select - 2;
                            }
                        }
                        var newIScroll = new iScroll(control, {bounce: false});
                        me.data("myScroll", newIScroll);
                        newIScroll.scrollToElement(me.find("li:eq(" + index_select + ")")[0]);
                        newIScroll.refresh();
                    }
                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove', function () {
            doItemClick = false;
        });
    });
    //slideButton
    $('.slideButton').on('click',function(){
        $(this).toggleClass('active');
        if($(this).hasClass('active')){
            $('.customerChoose p').removeClass('hide').addClass('show')
        }else{
            $('.customerChoose p').removeClass('show').addClass('hide')
        }
    });
    //高级搜索日期选择范围框
    $('ul.date').each(function (i, control) {
        var me=$(this);
        me.parent().parent().addClass('hasfocus');
        var fieldname=me.attr('for');
        var tablename=me.attr('tablename');
        var today=me.find('.today');
        var yesterday=me.find('.yesterday');
        var thisweek=me.find('.thisweek');
        var thismonth=me.find('.thismonth');
        var begindate=me.find('.begindate');
        var beginparent=begindate.parent();
        var enddate=me.find('.enddate');
        var endparent=enddate.parent();
        var isYesterday=function(begin,end){
            var now=new Date();
            var begincmp=now.addDays(-1).Format('yyyy-MM-dd');
            return begin==begincmp&&end==begincmp;
        };
        var isToday=function(begin,end){
            var now=new Date();
            var begincmp=now.Format('yyyy-MM-dd');
            return begin==begincmp&&end==begincmp;
        };
        var isThisWeek=function(begin,end){
            var now=new Date();
            var weekofday=(now.getDay()+6)%7;
            var begincmp=new Date(now.setDate(now.getDate() -weekofday)).Format('yyyy-MM-dd');
            var endcmp=new Date().Format('yyyy-MM-dd');
            return begin==begincmp&&end==endcmp;
        };
        var isThisMonth=function(begin,end){
            var now=new Date();
            var year = now.getFullYear();
            var month = now.getMonth()+1;
            if (month<10){
                month = "0"+month;
            }
            var begincmp =year+"-"+month+"-01";
            var endcmp=new Date().Format('yyyy-MM-dd');
            return begin==begincmp&&end==endcmp;
        };
        var checkSelect=function(){
            var begin=begindate.val();
            var end=enddate.val();
            me.find('.quickdate').removeClass('quickselect ');
            if(isYesterday(begin,end)){
                yesterday.addClass('quickselect ');
            }
            else if(isToday(begin,end)){
                today.addClass('quickselect ');
            }
            else if(isThisWeek(begin,end)){
                thisweek.addClass('quickselect ');
            }
            else if(isThisMonth(begin,end)){
                thismonth.addClass('quickselect ');
            }
        }
        today.bind('tap',function(){
            var $this=$(this);
            if($this.hasClass('quickselect')){
                $this.removeClass('quickselect');
                begindate.val('');
                enddate.val('');
            }
            else{
                me.find('.quickdate').removeClass('quickselect');
                $this.addClass('quickselect');
                var now=new Date();
                var begin=now.Format('yyyy-MM-dd');
                begindate.val(begin);
                enddate.val(begin);
            }
        });
        yesterday.bind('tap',function(){
            var $this=$(this);
            if($this.hasClass('quickselect')){
                $this.removeClass('quickselect');
                begindate.val('');
                enddate.val('');
            }
            else{
                me.find('.quickdate').removeClass('quickselect');
                $this.addClass('quickselect');
                var now=new Date();
                var begin=now.addDays(-1).Format('yyyy-MM-dd');
                var end=begin;
                begindate.val(begin);
                enddate.val(end);
            }
        });
        thisweek.bind('tap',function(){
            var $this=$(this);
            if($this.hasClass('quickselect')){
                $this.removeClass('quickselect');
                begindate.val('');
                enddate.val('');
            }
            else{
                me.find('.quickdate').removeClass('quickselect');
                $this.addClass('quickselect');
                var now=new Date();
                var weekofday=(now.getDay()+6)%7;
                var begin=new Date(now.setDate(now.getDate() -weekofday)).Format('yyyy-MM-dd');
                var end=new Date().Format('yyyy-MM-dd');
                begindate.val(begin);
                enddate.val(end);
            }
        });
        thismonth.bind('tap',function(){
            var $this=$(this);
            if($this.hasClass('quickselect')){
                $this.removeClass('quickselect');
                begindate.val('');
                enddate.val('');
            }
            else{
                me.find('.quickdate').removeClass('quickselect');
                $this.addClass('quickselect');
                var now=new Date();
                var year = now.getFullYear();
                var month = now.getMonth()+1;
                if (month<10){
                    month = "0"+month;
                }
                var begin =year+"-"+month+"-01";
                var end=new Date().Format('yyyy-MM-dd');
                begindate.val(begin);
                enddate.val(end);
            }
        });
        begindate.bind('change',function(){
            checkSelect();
        });
        enddate.bind('change',function(){
            checkSelect();
        });
    });


    $("ul.multSelect").each(function (i, control) {
        var me = $(control);
        me.parent().parent().addClass('hasfocus');
        var cName = me.attr("for");
        var value = me.attr("value");
        var tableName = me.attr("tableName");
		var formID = me.parents("form").attr("id");
        var childLi = me.html();
        me.html('<div>' + childLi + '</div>');

        /*        var selectLi=me.find('li[value="'+value +'"]');
         selectLi.addClass('select');*/
        var text = [];

        var values = value.split(",");
        $.each(values, function (i, val) {
            var selectLi = me.find('li[value="' + val + '"]');
            selectLi.addClass('select');
            text.push(selectLi.text());
        });
        var fc = me.parent();
		fc.attr("fc_for",cName);
		
        var input = $("<i class='arrow_right'></i><input type='hidden' name='" + cName + "' id='" + cName + "' value='" + value + "' tableName ='" + tableName + "'/><div class='select-value' id='form_text_" + cName + "'>" + (text.join("") == 0 ? "请选择" : text.join("、")) + "</div>");
        input.insertBefore(me);

        //me.find('li').click(function (event) {
        //    var select = $(this);
        //    select.hasClass('select') ? select.removeClass('select') : select.addClass('select');

        //});

        //修复小米，三星手机点击失灵
        var doItemClick = true;
        fc.bind('touchstart', function (e) {
            var select = $(this);
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
//                    me.find('li').unbind('touchstart').bind('touchstart', function (e) {
//                        var select = $(this);
//                        doItemClick = true;
//                        setTimeout(function () {
//                            if (doItemClick) {
//                                select.hasClass('select') ? select.removeClass('select') : select.addClass('select');
//                                doItemClick = true;
//                            }
//                        }, 100);
//                    }).unbind('touchmove').bind('touchmove', function () {
//                        doItemClick = false;
//                    });

                    me.find('li').unbind('tap').bind('tap', function (e) {
                        var select = $(this);
                        select.hasClass('select') ? select.removeClass('select') : select.addClass('select');
                    });

                    value = fc.find("#" + cName).val();
                    values = value.split(",");
                    me.find('li').removeClass("select");
                    $.each(values, function (i, val) {
                        var selectLi = me.find('li[value="' + val + '"]');
                        selectLi.addClass('select');
                    });
                    //if(event.target.id !="form_text_" + current){

                    current = cName;
                    var select = me;
                    $('input').blur();
                    window.scrollTo(0,me.offset().top);
                    select.appendTo("#mask");
                    var button = $('<ul class="buttonBox" style="margin-top:260px;"><button class="btn btn-block">确 定</button></ul>');
                    button.click(function (event) {
                        //if($("#mask").is(":hidden"))return false;
                        var selects = select.find('li.select');
                        if (selects.length > 0) {
                            var seelctValues = [], selectTexts = [];
                            selects.each(function (i, item) {
                                seelctValues.push($(item).attr("value"));
                                selectTexts.push($(item).text());
                            });
                            fc.find("#" + cName).val(seelctValues.join(","));
                            fc.find("#form_text_" + cName).html(selectTexts.join("、"));
                        }
                        else {
                            fc.find("#" + cName).val("");
                            fc.find("#form_text_" + cName).html("请选择");
                        }

                        select.appendTo(fc);
                        mask.html("").hide();
                        $("#popWin_mask_" + cName).css({opacity:0});
                        setTimeout(function(){
                            UI.creatPopWin_mask.remove("popWin_mask_" + cName);
                            current = null;
                            $("body").css("overflow", "auto");
                            document.removeEventListener("touchmove", handle, false);
                        },500);
                    });

                    UI.creatPopWin_mask.creat("popWin_mask_" + cName, null, null, 10003, formID);
                    mask.css("height", 305).css("z-index", 99999).append(button).show();
                    $("body").css("overflow", "hidden");
                    document.addEventListener("touchmove", handle, false);

                    var myScroll = me.data("myScroll");
                    if (typeof myScroll == "undefined") {
                        me.data("myScroll", new iScroll(control, {bounce: false}));
                    }
                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove', function () {
            doItemClick = false;
        });
    });

    $(".MultiLayerSelect").each(function (i, control) {
        var me = $(control);
        me.parent().parent().addClass('hasfocus');
        var fc = me.parent();
        var formID = me.parents("form").attr("id");

        var me_left = me.find(".MultiLayerSelect_left");
        var me_right = me.find(".MultiLayerSelect_right");

        var me_rightselct = me.find(".MultiLayerSelect_right ul li");
        var me_leftselct = me.find(".MultiLayerSelect_left li");

        var cleftName = me_left.attr("for");
        var vleftValue = me_left.attr("value");

        var crightName = me_right.attr("for");
        var vrightValue = me_right.attr("value");

        fc.attr("fc_for", cleftName);

        var selectleftLi = me_left.find('li[value="' + vleftValue + '"]');
        selectleftLi.addClass('select');
        var tright=me.find("div[parentcode='" + vleftValue + "']");
        tright.show();
        me.find("[parentcode='" + vleftValue + "'] ul").addClass('select');
        var selectrightLi = me_right.find('ul li[value="' + vrightValue + '"]');
        selectrightLi.addClass('select');

        var text = "请选择";
        if (selectleftLi.text() != "" ) {
            text = selectleftLi.text();
        }
        if(selectrightLi.text() != ""){
            text+= '-' + selectrightLi.text();
        }

        for(var i= 0,len=me_leftselct.length;i<len;i++){
            var $tleft=$(me_leftselct[i]);
            var tleftvalue=$tleft.attr('value');
            if(me.find("[parentcode='" + tleftvalue + "'] li").length>0){
                $tleft.append($("<i class='arrow_right'></i>"))
            }
        }

        //alert(text);

        //初始值

        //alert("<input type='hidden' name='"+cleftName+"' id='"+cleftName+"' value='"+vleftValue+"'/>");
        //alert("<input type='hidden' name='"+crightName+"' id='"+crightName+"' value='"+vrightValue+"'/>");
        //alert("<div class='select-value' id='form_text_"+ cleftName +"'>"+ (text==""?"请选择":text) +"</div>");
        var input = $("<i class='arrow_right'></i><input type='hidden' name='" +
            cleftName + "' id='" + cleftName + "' value='" + vleftValue + "'/><input type='hidden' name='"
            + crightName + "' id='" + crightName + "' value='" + vrightValue + "'/><div class='select-value' id='form_text_" + cleftName + "'>"
            +  text + "</div>");
        input.insertBefore(me);


        var currentcode;
        //修复小米，三星手机点击失灵
        var doItemClick = true;
        me_leftselct.bind('touchstart', function (e) {
            var selectleft = $(this);
            currentcode = selectleft.attr('value');
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    if(me.find("[parentcode='" + currentcode + "'] li").length==0){
                        me_left.find("li.select").removeClass('select');
                        selectleft.addClass('select');
                        me_right.find("ul.select").removeClass('select');
                        me_right.find("ul li.select").removeClass('select');
                        $("#mask").find('button').click();
                        return;
                    }

                    if (!selectleft.hasClass('select')) {
                        me_left.find("li.select").removeClass('select');
                        selectleft.addClass('select');
                        me_right.find("ul.select").removeClass('select');
                        var curright = me.find("[parentcode='" + currentcode + "']");
                        me_right.find('div').hide();
                        curright.closest('div').show();
                        curright.find('ul').addClass('select');
                        //赋值

                        $("body").css("overflow", "hidden");
                        document.addEventListener("touchmove", handle, false);

                        var myScroll1 = me.data("myScroll_"+currentcode);
                        if (typeof myScroll1 == "undefined") {
                            myScroll1 = new iScroll(curright[0], { bounce: false });
                            me.data("myScroll_"+currentcode, myScroll1);
                        }
                        myScroll1.refresh();
                    }
                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove', function () {
            doItemClick = false;
        });

        me_rightselct.bind('touchstart', function (e) {
            var selectright = $(this);
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    if (!selectright.hasClass('select')) {
                        me_right.find("ul li.select").removeClass('select')
                        selectright.addClass('select');

                    }
                    $("#mask").find('button').click();

                    $("body").css("overflow", "auto");
                    document.removeEventListener("touchmove", handle, false);
                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove', function () {
            doItemClick = false;
        });

        fc.bind('touchstart', function (e) {
            var select = $(this);
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    $('input').blur();
                    window.scrollTo(0,me.offset().top);
                    //if (event.target.id != "form_text_" + current) {
                    $("#mask").find('button').click();
                    current = cleftName;
                    //点击录入部分
                    me.appendTo("#mask");

                    var button = $('<button class="btn btn-block" style="display:none">确 定</button>');
                    button.click(function (event) {
                        var leftselect = me_left.find("li.select");
                        var rightselect = me_right.find("ul li.select");

                        if(leftselect.length >0&&rightselect.length == 0){
                            fc.find("#form_text_" + cleftName).text(leftselect.text() );
                            fc.find("#" + cleftName).val(leftselect.attr("value"));
                            fc.find("#" + crightName).val(leftselect.attr("value"));
                        }
                        else if(leftselect.length >0&&rightselect.length > 0){
                            fc.find("#form_text_" + cleftName).text(leftselect.text() + "-" + rightselect.text());
                            fc.find("#" + cleftName).val(leftselect.attr("value"));
                            fc.find("#" + crightName).val(rightselect.attr("value"));
                        }
                        else{
                            fc.find("#form_text_" + cleftName).text("请选择");
                            fc.find("#" + cleftName).val();
                            fc.find("#" + crightName).val();
                        }
                        me.appendTo(fc);
                        mask.html("").hide();
                        $("#popWin_mask_Multi").css({opacity:0});
                        setTimeout(function(){
                            UI.creatPopWin_mask.remove("popWin_mask_Multi");
                            current = null;
                            $("body").css("overflow", "auto");
                            document.removeEventListener("touchmove", handle, false);
                        },500);
                    })
                    UI.creatPopWin_mask.creat("popWin_mask_Multi", null, null, 10003, formID);
                    $("#mask").css("z-index", 99999).append(button).show();

                    $("body").css("overflow", "hidden");
                    document.addEventListener("touchmove", handle, false);

                    var myScroll = me.data("myScroll");
                    if (typeof myScroll == "undefined") {
                        me.data("myScroll", new iScroll(me_left[0], { bounce: false }));
                    }
                    if(tright.length>0){
                        var myScroll1 = me.data("myScroll_"+vleftValue);
                        if (typeof myScroll1 == "undefined") {
                            myScroll1 = new iScroll(tright[0], { bounce: false });
                            me.data("myScroll_"+vleftValue, myScroll1);
                        }
                        myScroll1.refresh();
                    }

                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove', function () {
            doItemClick = false;
        });
    });
    //UI.formErrorMsg.send("text1","姓名必须填写");
    //UI.formErrorMsg.remove("text1");

    //获取选中，写入隐藏域
    function getPPSelectChoosed(obj, inputHidden) {
        var obj = $(obj);
        var choosed = obj.find(".select");
        var choosedKV = "";
        for (var i = 0; i < choosed.length; i++) {
            if (i > 0)choosedKV += ",";
            choosedKV += $(choosed[i]).attr("value");
        }
        inputHidden.val(choosedKV);
    }

    //平铺所有选项，多选
    $("ul.PPmultSelect").each(function (i, control) {
        var me = $(control);
        var cName = me.attr("for");
        var value = me.attr("value");
        var tableName = me.attr("tableName");
        var values = value.split(",");
        $.each(values, function (i, val) {
            var selectLi = me.find('li[value="' + val + '"]');
            selectLi.addClass('select');
        });

        var inputHidden = $('<input type="hidden" class="chooseHidden" name="' + cName + '" id="' + cName + '" value="' + value + '" tableName = "' + tableName + '" />');
        me.append(inputHidden);
        me.find('li').click(function (event) {
            if ($(this).hasClass("select")) {
                $(this).removeClass("select");
            } else {
                $(this).addClass("select");
            }
            getPPSelectChoosed(me, inputHidden);
        });
    });
    var doItemClick = true;
    //选择房源
    $('.form-control .roomselect').each(function(i, control){
        var me=$(control);
        var parent=me.parent();
        parent.parent().addClass('hasfocus');
        var hiddenvalue=parent.find('.hiddenvalue');
        var clearvalue=parent.find('.clear-value');
        parent.bind('touchstart',function(){
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    $('input').blur();
                    window.scrollTo(0,me.offset().top);
                    var rangetype=me.attr('rangetype');
                    var issale=me.attr('issale');
                    SelectRoom.setScroll();
                    SelectRoom.showBuilding();
                    SelectRoom.searchBuildUrl=SelectRoom.tBuildUrl1;
                    SelectRoom.searchRoomUrl=SelectRoom.tRoomUrl1;
                    SelectRoom.selectType="1";
                    SelectRoom.selectRoom=function(){
                        var $this = $(this);
                        var roominfo = $this.attr('roominfo').split('-');
                        roominfo.splice(0, 1);
                        roominfo = roominfo.join('-');
                        hiddenvalue.val($this.attr('room_guid'));
                        me.html(roominfo.toString());
                        clearvalue.show();
                        SelectRoom.restoreScroll();
                        setTimeout(function(){
                            $('#dicSelectRoomPanel').hide();
                        },500);
                    };
                    SelectRoom.loadBuilding(SelectRoom.tBuildData);
                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove',function(){
            doItemClick = false;
        });

        clearvalue.bind('touchstart',function(e){
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    e.stopPropagation();
                    hiddenvalue.val('');
                    me.html('');
                    clearvalue.hide();
                    doItemClick = false;
                    setTimeout(function(){
                        doItemClick=true;
                    },500);
                }
            }, 500);
        }).bind('touchmove',function(){
            doItemClick = false;
        });
        if(hiddenvalue.val()==''){
            clearvalue.hide();
        }
        else{
            clearvalue.show();
        }
    });


    //平铺所有选项，单选
    $("ul.PPsingleSelect").each(function (i, control) {
        var me = $(control);
        var cName = me.attr("for");
        var value = me.attr("value");
        var tableName = me.attr("tableName");
        me.find('li[value="' + value + '"]').addClass('select');

        var inputHidden = $('<input type="hidden" class="chooseHidden" name="' + cName + '" id="' + cName + '" value="' + value + '" tableName ="' + tableName + '" />');
        me.append(inputHidden);
        var doItemClick=true;
        me.find('li').bind('touchstart', function (e) {
            var select = $(this);
            doItemClick = true;
            setTimeout(function () {
                if (doItemClick) {
                    if (select.hasClass("select")) {
                        select.removeClass("select");
                    } else {
                        select.addClass("select").siblings().removeClass("select");
                        if(me.parents("form").find("div[for='"+cName+"']"))
                        {
                            UI.formErrorMsg.remove(cName);
                        }
                    }
                    getPPSelectChoosed(me, inputHidden);
                    doItemClick = true;
                }
            }, 500);
        }).bind('touchmove', function () {
            doItemClick = false;
        });
    });

    //panel_group_title展开收起
    $(".panel_group_title.expand").off().tap("click", function () {
        var countNum = $.trim($(this).find(".font_alert").text()) * 1;
        //是否有数值
        var panel_group_title_Flag = (countNum == 0) ? true : false;
        if (panel_group_title_Flag)return false;
        //是否已展开
        var panel_group_content = $(this).next(".panel_group_content");

        panel_group_titleSilderFlag = 0;
        if ($(this).hasClass("select")) {
            $(this).removeClass("select");
            panel_group_content.slideDown(400,function(){
			    try{
					myScroll.refresh();
					myScroll2.refresh();
				}catch(e){
					
				}	
			});
        } else {
            $(this).addClass("select");
            panel_group_content.slideUp(400,function(){
			    try{
					myScroll.refresh();
					myScroll2.refresh();
				}catch(e){
					
				}	
			});
        }
		
    });

    //拨打电话弹层
    $(".callPhone_js").on("click", function () {
        $("#callPhoneLayer").remove();
        var telPhoneNum = $(this).attr("telPhoneNum");
        var callPhoneLayer = $('<div id="callPhoneLayer"><ul>是否拨打电话？</ul><ul><a class="" id="cancelCall">取消</a><a href="tel:' + telPhoneNum + '">确定</a></ul></div>');
        $(callPhoneLayer).showPopWin();
    });


//	//创建sliderBar,基于jquery-ui
//	$(".slider-bar-box").each(function(i,control){
//		var me = $(control);
//		var minV = me.attr("min")*1;
//		var maxV = me.attr("max")*1;
//		var value = me.attr("value")*1;
//		var cName = me.attr("for");
//		
//		var silderUL = $('<ul></ul>');
//		var silderHidden = $("<input type='hidden' name='"+cName+"' id='"+cName+"' value='"+value+"'/>");
//		var silderMin = $('<li class="silder-min">'+minV+'</li>');
//		var silderMax = $('<li class="silder-max">'+maxV+'</li>');
//		var silderBox = $('<li class="silder-bar-copy"></li>');
//		var silderBar = $('<span class="silder-bar"></span>');
//		
//		silderBox.append(silderBar);
//		silderUL.append(silderMin,silderBox,silderMax);
//		me.append(silderUL,silderHidden);
//		var sliderValue = $('<span class="slider-value"></span>');
//		silderBar.slider({
//			min:minV,
//			max:maxV,
//			step:1,
//			range: "min",
//			animate: "fast",
//			value:value,
//			slide: function( event, ui ) {
//				changeVal(ui.value);
//			},
//			create: function( event, ui ) {
//				sliderValue.text(value);
//				$(".ui-slider-handle").append(sliderValue);
//			}
//		});
//		function changeVal(val){
//			silderBar.slider({value:val});
//			sliderValue.text(val);
//			silderHidden.val(val);
//			me.attr("value",val);
//		}
//		silderMin.click(function(){
//			changeVal(minV);
//		});
//		silderMax.click(function(){
//			changeVal(maxV);
//		});
//	});
});

//创建弹出层
(function ($) {
    $.fn.showPopWin = function (options) {
        return new showPopWin(this, options);
    };
    showPopWin = function (el, options) {
        var obj = $(el);
        var settings = $.extend({}, $.showPopWin.defaults, options || {});
        obj.maskSetting = settings.maskSetting;
        obj.needScorll = settings.needScorll;

        obj.id = $(el).attr("id") ? $(el).attr("id") : randomByMM(1, 10000);
        //需要特殊设置mask样式
        if (obj.maskSetting) {
            UI.creatPopWin_mask.creat("popWin_mask_" + obj.id, "#ffffff", 1);
        } else {
            UI.creatPopWin_mask.creat("popWin_mask_" + obj.id);
        }
        obj.mask = $("#popWin_mask_" + obj.id);
        if ($('#popWin_box_' + obj.id).length > 0) {
            obj.popWin = $('#popWin_box_' + obj.id);
        } else {
            obj.popWin = $('<div class="popWin_box" id="popWin_box_' + obj.id + '"></div>');
        }
        obj.closeF = settings.closeF;
        obj.position = settings.position;
        var maxHeight = UI.windowHeight;

        $.extend(obj, {
            init: function () {
                if (obj.closeF) {
                    obj.closeFun();
                } else {
                    $("body").append(obj.popWin);
                    //需要模拟滚动条
                    if (obj.needScorll) {
                        obj.popWin.attr("style", "height:" + maxHeight + "px;");
                        $(el).attr("style","width:100%;position:absolute;height:"+maxHeight+"px;overflow:hidden;");
                        obj.popWin.append($(el));
                    } else {
                        obj.popWin.append(obj);
                    }
					if (obj.maskSetting) {
						obj.popWin.attr("style","height:"+maxHeight+"px;overflow:auto;");
					}

                    obj.showFun();
                    if (obj.position == "center") {
                        var newWidth = obj.mask.width() - 30;
                        obj.popWin.css({"top": "20%", "margin": "15px", "width": newWidth, "border-radius": 5});
                    }
                }
            },
            showFun: function () {
                obj.mask.show();
                obj.popWin.show();
                obj.fadeIn(400, function () {
                    //需要模拟滚动条
                    if (obj.needScorll) {
                        $("body").css("overflow", "hidden");
                        document.addEventListener("touchmove", handle, false);
                        var myScroll = $(el).data("myScroll");
                        if (typeof myScroll == "undefined") {
                            $(el).data("myScroll", new iScroll($(el)[0], {bounce: false}));
                        }
                    }
                });
            },
            closeFun: function () {
                obj.mask.hide();
                obj.popWin.hide();
                obj.fadeOut(400,function(){
                    $("body").css("overflow", "auto");
                    document.removeEventListener("touchmove", handle, false);
					scrollTopAuto(null,0,500);//底部页面跳转到顶部
                });
            }
        });
        obj.init();
        obj.mask.on("click", function () {
            obj.closeFun();
        });
    }
    $.showPopWin =
    {//默认配置属性
        defaults: {
            closeF: false,
            position: "top",
            maskSetting: false,
            needScorll: false
        }
    }
})($);

//单选项前端格式化$("ul.singleSelect")
(function ($) {
    $.fn.singleSelect = function (options) {
        return new singleSelect(this, options);
    };
    singleSelect = function (el, options) {
        var settings = $.extend({}, $.singleSelect.defaults, options || {});
		var me = $(el);
        me.cName = me.attr("for");
        me.valueDefault = me.attr("value");
        me.tableName = me.attr("tableName");
        me.childLi = me.html();
        me.html('<div>' + childLi + '</div>');
        me.fc = me.parent();
		
		var showHtml = $("<i class='arrow_right'></i><input type='hidden' name='" + cName + "' id='" + cName + "' value='" + value + "' tableName ='" + tableName + "'/><div class='select-value' id='form_text_" + cName + "'></div>");
		showHtml.insertBefore(me);
		
		me.form_text = me.find("#form_text_"+cName);
		me.inputHidden = me.find("#"+cName);

		$.extend(me, {
			init:function(){
				me.chooseFun();

                var doItemClick = true;
                me.find('li').bind('touchstart', function (e) {
                    var select = $(this);
                    doItemClick = true;
                    setTimeout(function () {
                        if (doItemClick) {
                            if (select.hasClass('select')) {
                                select.removeClass('select');
                            } else {
                                select.addClass("select").siblings().removeClass('select');
                            }
                            me.closeFun();
                            doItemClick = true;
                        }
                    }, 500);
                }).bind('touchmove', function () {
                    doItemClick = false;
                });
			},
			chooseFun:function(){
				var choosed_li = me.find("li.select");
				var choosed_text = "请选择",choosed_value = "";
				if(choosed_li.length > 0){
					choosed_text =  choosed_li.text();
					choosed_value =  choosed_li.attr("value");
				}
				me.form_text.text(choosed_text);
				me.inputHidden.val(choosed_value);
			},
			openFun:function(){
				
			},
			closeFun:function(){
				
			}
		});
		
		
	};
	$.singleSelect =
    {//默认配置属性
        defaults: {}
    }
})($);