//Function扩展
Function.prototype.extend = function (name, func) {
    /// <summary>对象扩展</summary>
    /// <param name="name" type="string">方法名称</param>
    /// <param name="func" type="int">方法体</param>
    var me = this;
    if (typeof arguments[0] == "object") {
        $.each(arguments[0],
        function (key, fn) {
            me.prototype[key] = fn;
        })
    } else if (typeof arguments[0] == "string") {
        me.prototype[arguments[0]] = arguments[1];
    }
};

Function.extend('inherits', function (parent) {
    /// <summary>类的继承</summary>
    /// <param name="parent" type="object">父类</param>
    if (arguments.length == 1) {
        this.prototype = new parent();
    } else {
        for (var i = 1; i < arguments.length; i++) {
            var name = arguments[i];
            this.prototype[name] = parent.prototype[name];
        }
    }
    return this;
});

Number.extend({
    round: function (n) {
        /// <summary>数字进行特定保留位处理</summary>
        /// <param name="n" type="int">小数位数</param>
        n = Math.pow(10, n || 0).toFixed(n < 0 ? -n : 0);
        return Math.round(this * n) / n;
    },
    add: function (num, round) {
        /// <summary>加法运算</summary>
        /// <param name="num" type="float">被加数</param>
        /// <param name="round" type="int">保留几位小数</param>
        num = num.toString().replace(/,/g, "");
        if (isNaN(num)) { throw "被加数不能转化成数字" }
        round = round == undefined ? 2 : round.toString().toInt();
        num = num.toFloat();
        var r1 = 0, r2 = 0, arr1 = this.toString().split("."), arr2 = num.toString().split(".");
        if (arr1.length > 1) { r1 = arr1[1].length };
        if (arr2.length > 1) { r2 = arr2[1].length };
        var m = Math.pow(10, Math.max(r1, r2));
        return ((this * m + num * m) / m).round(round);
    },
    sub: function (num, round) {
        /// <summary>减法运算</summary>
        /// <param name="num" type="float">被减数</param>
        /// <param name="round" type="int">保留几位小数</param>
        num = num.toString().replace(/,/g, "");
        if (isNaN(num)) { throw "被加数不能转化成数字" }
        round = round == undefined ? 2 : round.toString().toInt();
        num = num.toFloat();
        var r1 = 0, r2 = 0, arg1 = this.toString().split("."), arg2 = num.toString().split(".");
        if (arg1.length > 1) { r1 = arg1[1].length };
        if (arg2.length > 1) { r2 = arg2[1].length };
        var m = Math.pow(10, Math.max(r1, r2));
        return ((this * m - num * m) / m).round(round);
    },
    mul: function (num, round) {
        /// <summary>乘法运算</summary>
        /// <param name="num" type="float">被乘数</param>
        /// <param name="round" type="int">保留几位小数</param>
        num = num.toString().replace(/,/g, "");
        if (isNaN(num)) { throw "被加数不能转化成数字" }
        round = round == undefined ? 2 : round.toString().toInt();
        num = num.toFloat();
        var r1 = 0, r2 = 0, arg1 = this.toString().split("."), arg2 = num.toString().split(".");
        if (arg1.length > 1) { r1 = arg1[1].length };
        if (arg2.length > 1) { r2 = arg2[1].length };
        return ((this * Math.pow(10, r1) * num * Math.pow(10, r2)) / Math.pow(10, r1.add(r2))).round(round);
    },
    div: function (num, round) {
        /// <summary>除法运算</summary>
        /// <param name="num" type="float">被除数</param>
        /// <param name="round" type="int">保留几位小数</param>
        num = num.toString().replace(/,/g, "");
        if (isNaN(num)) { throw "被加数不能转化成数字" }
        else if (new Number(num) == 0) {
            return "";
        }
        round = round == undefined ? 2 : round.toString().toInt();
        var r1 = 0, r2 = 0, arr1 = this.toString().split("."), arr2 = num.toString().split(".");
        if (arr1.length > 1) { r1 = arr1[1].length };
        if (arr2.length > 1) { r2 = arr2[1].length };
        var m = Math.pow(10, Math.max(r1, r2));
        return ((this * m) / (num * m)).round(round);
    }
});

String.extend({
    getByteLength: function () {
        /// <summary>获取字符串的字节数</summary>
        return this.replace(/[^\x00-\xff]/g, "ci").length;
    },   
    trim: function () {
        /// <summary>去掉字符串两边的空格</summary>
        return $.trim(this);
    },   
    toDate: function (splitChar) {
        /// <summary>只穿转化为日期</summary>
        /// <param name="splitChar" type="String">日期分隔符号，默认为-</param>
        splitChar = splitChar || "-";
        var reg = new RegExp(splitChar, "g")
        return new Date(this.replace(reg, '/'));
    },
    toDateString: function () {
        //日期将字符串格式化为："yyyy-MM-dd"
        if (this == "" || this == null || this == undefined) {
            return "";
        }
        return this.toDate().format("yyyy-MM-dd");
    },
    toBool: function () {
        var str = this.toString().toLocaleLowerCase();
        if (str === "1" || str === "true") {
            return true;
        } else if (str === "0" || str === "false") {
            return false;
        }
    },
    toInt: function () {
        /// <summary>转化为整数</summary>
        return parseInt(this, 10);
    },
    toFloat: function (round) {
        /// <summary>转化为整数</summary>
        /// <param name="round" type="int">小数保留位</param>
        var num = this.replace(/,/g, "");
        var res = (isNaN(num) || num == "") ? 0 : parseFloat(num);
        return round ? res.round(round) : res;
    },
    isNumber: function () {
        var num = this.replace(/,/g, "");
        return !(isNaN(num) || num == "");
    },
    escape: function () {
        if (myEscape) {
            return myEscape(this);
        } else {
            throw ("需要引入/_controls/util/util.js");
        }
    }
});