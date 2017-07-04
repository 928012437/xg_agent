//Jquery Mobile Toast提示
//参数：    msg:提示信息
var toast = function(msg, top, time) {
    if (top == undefined) {
        top = ($(window).height() - 200) / 2;
    }
    if (time == undefined) {
        time = 3000;
    }
    $("<div style='color:white;background:#000000;padding:10px 0px 10px 0px;border:0px;'><p>" + msg + "</p></div>").css({
        display: "block",
        opacity: 0.70,
        position: "fixed",
        padding: "7px",
        "text-align": "center",
        width: "200px",
        "font-size": "14px",
        "border-radius": "10px",
        "border": "0px",
        left: ($(window).width() - 215) / 2,
        top: top
    })
    .appendTo("#mask").parent().show().delay(time)    
    .fadeOut(400, function() {
        $(this).html("");
    });
}

//Jquery Mobile Toast提示
//参数：    msg:提示信息
var newtoast = function(msg, top, time) {
    if (top == undefined) {
        top = ($(window).height() - 200) / 2;
    }
    if (time == undefined) {
        time = 3000;
    }
    $("<div style='color:white;background:#000000;padding:10px 0px 10px 0px;border:0px;'><p>" + msg + "</p></div>").css({
        display: "block",
        opacity: 0.70,
        position: "fixed",
        padding: "7px",
        "text-align": "center",
        width: "200px",
        "font-size": "14px",
        "border-radius": "10px",
        "border": "0px",
        left: ($(window).width() - 215) / 2,
        top: top
    })
    .appendTo("#msg").delay(time)
    .fadeOut(400, function() {
        $(this).remove();
    });
}

//公用部分
var prefix = '',vendors = { Webkit: 'webkit', Moz: '', O: 'o', ms: 'MS' },
testEl = document.createElement('div');
$.each(vendors, function(vendor, event){
    if (testEl.style[vendor + 'TransitionProperty'] !== undefined) {
      prefix = '-' + vendor.toLowerCase() + '-';
      return false;
    }
});

/**
 *  消息组件
 */
var wktoast = (function($){
    var TOAST_DURATION = 5000;
    //定义模板
    var TEMPLATE = {
        toast : '<a href="#">{value}</a>',
        success : '<a href="#"><i class="icon checkmark-circle"></i>{value}</a>',
        error : '<a href="#"><i class="icon cancel-circle"></i>{value}</a></div>',
        info : '<a href="#"><i class="icon info-2"></i>{value}</a>'
    }
    var toast_type = 'toast',_toast,timer;
    var _init = function(){
        //全局只有一个实例
        $('body').append('<div id="wk_toast"></div>');
        _toast = $('#wk_toast');
        _subscribeCloseTag();  暂时没明白什么意思
    }

    /**
     * 关闭消息提示
     */
    var hide = function(){
        J.anim(_toast,'scaleOut',function(){
            _toast.hide();
           _toast.empty();
        });
    }
    /**
     * 显示消息提示
     * @param type 类型  toast|success|error|info
     * @param text 文字内容
     * @param duration 持续时间 为0则不自动关闭,默认为5000ms
     */
    var show = function(type,text,duration){
        if(timer) clearTimeout(timer);
        toast_type = type;
        _toast.attr('class',type).html(TEMPLATE[type].replace('{value}',text)).show();
        J.anim(_toast,'scaleIn');
        if(duration !== 0){//为0 不自动关闭
            timer = setTimeout(hide,duration || TOAST_DURATION);
        }
    }
    var _subscribeCloseTag = function(){
        _toast.find('[data-target="close"]').on('tap',function(){
            hide();
        })
    }
    _init();
    return {
        show : show,
        hide : hide
    }
});

/**
*动画效果变量
*/
var animationName = prefix + 'animation-name';
var animationDuration= prefix + 'animation-duration';
var animationTiming= prefix + 'animation-timing-function';
var transitionProperty = prefix + 'transition-property';
var transitionDuration = prefix + 'transition-duration';
var transitionTiming   = prefix + 'transition-timing-function';
var supportedTransforms = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i;
//自定义动画时的默认动画函数(非page转场动画函数)
var transitionTimingFunc = 'ease-in';
/**
 *  动画组件
 @ele:Dom元素
 @type:动画效果类型(scaleIn;scaleOut)
 @duration:时间间隔(单位秒,可以为小数)
 */
var anim=function(ele,type,duration,callback){
    var key, cssValues = {}, cssProperties, transforms = '',
        that = this, wrappedCallback;

    if (duration === undefined) {
        duration = 0.4;
    }
    
    if (typeof properties == 'string') {
      // keyframe animation
      cssValues[animationName] = properties
      cssValues[animationDuration] = duration + 's'
      cssValues[animationTiming] = (ease || 'linear')
    } else {
        cssProperties = []
        // CSS transitions
        for (key in properties){
            if (supportedTransforms.test(key)){
                transforms += key + '(' + properties[key] + ') ';
            }else{
                cssValues[key] = properties[key], cssProperties.push(dasherize(key));
            }
        }
    }

    wrappedCallback = function(event){
        ele.css(cssReset)
        callback && callback.call(this)
    }
    if (duration > 0) {
        ele.bind(endEvent, wrappedCallback);
    }

    ele.css(cssValues)

    if (duration <= 0){
        setTimeout(function() {
            that.each(function(){ wrappedCallback.call(this) })
        }, 0);
    }
 };