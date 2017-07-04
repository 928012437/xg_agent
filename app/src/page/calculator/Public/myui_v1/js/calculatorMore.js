var calculatorMore = function () {
    function init() {
        //var formValue = {
        //    //贷款类型(1商业,2公积金,3组合)
        //    dklx: QueryString('dklx'),

        //    //还款方式(1等额本息,2等额本金)
        //    hkfs: QueryString('hkfs'),

        //    //贷款总额
        //    dkze: parseFloat(QueryString('dkze')),

        //    //贷款月数
        //    dkys: parseFloat(QueryString('dkys')),

        //    //货款利率
        //    dkll: parseFloat(QueryString('dkll')),

        //    //货款月利率
        //    dkyll: parseFloat(QueryString('dkyll')),

        //    //月均还款
        //    yjhk: parseFloat(QueryString('yjhk')),

        //    //支付利息
        //    zflx: parseFloat(QueryString('zflx')),

        //    //还款总额
        //    hkze: parseFloat(QueryString('hkze')),

        //    //组合商业贷款总额
        //    sdkze: parseFloat(QueryString('sdkze')),

        //    //组合公积金贷款总额
        //    gdkze: parseFloat(QueryString('gdkze'))
        //}

        var ygArray = [];
        if (!formValue) return;
        //加载贷款详情
        $("#td_dklx").html(formValue.dklx_text);
        $("#td_hkfs").html(formValue.hkfs_text);
        $("#td_dkze").html(parseFloat(formValue.dkze) / 10000 + "万元");
        $("#td_dkys").html(formValue.dkys / 12 + "年");
        $("#td_dkll").html(Number(formValue.dkll).mul(100, 4) + "%");

        if (formValue.dklx == '3') {
            if (formValue.hkfs == '2') {
                for (var i = 0; i < formValue.dkys; i++) {
                    var stage = i + 1;
                    //偿还本金(商业)
                    var schbj = formValue.sdkze / formValue.dkys;
                    //偿还利息(商业)
                    var schlx = (formValue.sdkze - schbj * i) * formValue.dkyll;
                    //偿还本金(公积金)
                    var gchbj = formValue.gdkze / formValue.dkys;
                    //偿还利息(公积金)
                    var gchlx = (formValue.gdkze - gchbj * i) * 0.00375;

                    ygArray.push('<tr><td class="left">');
                    ygArray.push(stage);
                    ygArray.push('</td>');
                    ygArray.push('<td class="right">');
                    ygArray.push(numberberComma(schbj + schlx + gchbj + gchlx));
                    ygArray.push('</td></tr>');
                }
            }
        }
        else {
            if (formValue.hkfs == '2') {
                for (var i = 0; i < formValue.dkys; i++) {
                    var stage = i + 1;
                    //偿还本金
                    var chbj = formValue.dkze / formValue.dkys;
                    //偿还利息
                    var chlx = (formValue.dkze - chbj * i) * formValue.dkyll;

                    ygArray.push('<tr><td class="left">');
                    ygArray.push(stage);
                    ygArray.push('</td>');
                    ygArray.push('<td class="right">');
                    ygArray.push(numberberComma(chbj + chlx));
                    ygArray.push('</td></tr>');
                }
            }
        }
        if (ygArray.length == 0) {
            $("#rfdmonthdetails table").hide();
        } else {
            $("#rfdmonthdetails table").show();
            $('#rfdmonthdetails tbody').html(ygArray.join(''));
        }
        
    }

    /*千分位*/
    function numberberComma(number, decimal) {
        var regex = /(-?\d+)(\d{3})/;
        decimal = decimal || 2;
        number = number.toFixed(decimal);

        while (regex.test(number)) {
            number = number.replace(regex, '$1,$2');
        }
        return number;
    }

    /*获取url参数*/
    function QueryString(paramName) {
        var searchStr = location.search.substr(1);
        if (searchStr.length == 0)
            return null;
        var collection = searchStr.split('&');
        for (var i = 0; i < collection.length; i++) {
            var tmp = collection[i].split('=');
            if (tmp.length < 2)
                continue;
            if (tmp[0].toUpperCase() == paramName.toUpperCase())
                return tmp[1];
        }
        return null;
    }

    return {
        init: init
    }
}();

calculatorMore.init();