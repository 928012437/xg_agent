String.prototype.format = function () {
    var args = arguments;
    return this.replace(/\{(\d+)\}/g, function (m, i) {
        return args[i];
    });
}
var formValue;
var arraydklx = ["商业", "公积金", "组合"];
var arrayhkfs = ["等额本息", "等额本金"];
var calculator = function () {
    function init() {
        $('#dkze').focus(function () {
            $(this).val('');
        });
        /*贷款利率*/
        $('#ll,#bs').change(function () {
            var ll = parseFloat($('#ll').val() || 0);
            var bs = parseFloat($('#bs').val() || 0);
            $('.dkll').val((ll * bs).toFixed(2));
            $("#dkll_bs").val((ll * bs).toFixed(2));
        });
    }

    function calculate() {
        formValue = null;
        count();
        calculatorMore.init();
        $('#submit').css("background-color", "#53d769");
        //$('#result_box').slideDown();
        $('#result_box').show();
        //scrollTopAuto(null, 1000);
        setTimeout(function () {
            $('#submit').css("background-color", "#53d769");
        }, 500);
    }

    /*统计房贷*/
    function count() {
        formValue = {
            //贷款类型(1商业,2公积金,3组合)
            dklx: $('#dklx').val(),
            dklx_text: arraydklx[parseInt($('#dklx').val()-1)],

            //还款方式(1等额本息,2等额本金)
            hkfs: $('#hkfs').val(),
            hkfs_text:arrayhkfs[parseInt($('#hkfs').val()-1)],

            //贷款总额
            dkze: parseFloat($('#dkze').val() || 0) * 10000,

            //贷款月数
            dkys: parseFloat($('#dkys').val() || 0),

            //货款利率
            dkll: parseFloat($('.dkll').val() || 0) / 100,

            //货款月利率
            dkyll: 0,

            //月均还款
            yjhk: 0,

            //支付利息
            zflx: 0,

            //还款总额
            hkze: 0,

            //组合商业贷款总额
            sdkze: 0,

            //组合公积金贷款总额
            gdkze: 0
        }
        formValue['dkyll'] = formValue.dkll / 12;

        /*贷款类型为组合时*/
        if (formValue.dklx == '3') {
            //商业贷款总额
            formValue['sdkze'] = parseFloat($('#sydk').val() || 0) * 10000;
            //公积金贷款总额
            formValue['gdkze'] = parseFloat($('#gjj').val() || 0) * 10000;
            //贷款总额
            formValue.dkze = formValue['sdkze'] + formValue['gdkze'];
            //公积金货款月利率：0.045/12=0.00375
            if (formValue.hkfs == '1') {
                formValue['yjhk'] = (formValue['sdkze'] * formValue['dkyll'] * Math.pow((1 + formValue['dkyll']), formValue.dkys)) / (Math.pow((1 + formValue['dkyll']), formValue.dkys) - 1) +
                                    (formValue['gdkze'] * 0.00375 * Math.pow(1.00375, formValue.dkys)) / (Math.pow(1.00375, formValue.dkys) - 1);

                formValue['hkze'] = formValue['yjhk'] * formValue.dkys;
                formValue['zflx'] = formValue['hkze'] - formValue.dkze;
            }
            else if (formValue.hkfs == '2') {
                formValue['yjhk'] = formValue['sdkze'] / formValue.dkys + formValue['sdkze'] * formValue['dkyll'] +
                                    formValue['gdkze'] / formValue.dkys + formValue['gdkze'] * 0.00375;

                formValue['zflx'] = ((formValue['sdkze'] / formValue.dkys + formValue['sdkze'] * formValue['dkyll']) + formValue['sdkze'] / formValue.dkys * (1 + formValue['dkyll'])) / 2 * formValue.dkys - formValue['sdkze'] +
                                    ((formValue['gdkze'] / formValue.dkys + formValue['gdkze'] * 0.00375) + formValue['gdkze'] / formValue.dkys * 1.00375) / 2 * formValue.dkys - formValue['gdkze'];

                formValue['hkze'] = formValue['zflx'] + formValue.dkze;
            }
        }
        else {
            /*等额本息*/
            if (formValue.hkfs == '1') {
                formValue['yjhk'] = (formValue.dkze * formValue.dkyll * Math.pow((1 + formValue.dkyll), formValue.dkys)) / (Math.pow((1 + formValue.dkyll), formValue.dkys) - 1);
                formValue['hkze'] = formValue['yjhk'] * formValue.dkys;
                formValue['zflx'] = formValue['hkze'] - formValue.dkze;
            }
                /*等额本金*/
            else if (formValue.hkfs == '2') {
                formValue['yjhk'] = formValue.dkze / formValue.dkys + formValue.dkze * formValue.dkyll;
                formValue['zflx'] = ((formValue.dkze / formValue.dkys + formValue.dkze * formValue.dkyll) + formValue.dkze / formValue.dkys * (1 + formValue.dkyll)) / 2 * formValue.dkys - formValue.dkze;
                formValue['hkze'] = formValue['zflx'] + formValue.dkze;
            }
        }

        /*等额本息时隐藏更多*/
        var strTemplet;
        if (formValue.hkfs == '1') {
            strTemplet = '<tr><td>还款总额</td><td>{0}元</td></tr><tr><td>贷款月数</td><td>{1}月</td></tr><tr><td>支付利息</td><td>{2}元</td></tr><tr><td>月均还款</td><td><span class="red-color">{3}</span>元</td></tr>';
            $('tfoot').hide();
        }
        else {
            strTemplet = '<tr><td>还款总额</td><td>{0}元</td></tr><tr><td>贷款月数</td><td>{1}月</td></tr><tr><td>支付利息</td><td>{2}元</td></tr><tr><td>首月还款</td><td><span class="red-color">{3}</span>元</td></tr>';
            $('tfoot').show();
        }
        strTemplet = strTemplet.format(numberberComma(formValue['hkze']), formValue['dkys'], numberberComma(formValue['zflx']), numberberComma(formValue['yjhk']));
        $('#refund tbody').html(strTemplet);

        /*点击更多详情跳转*/
        //$('tfoot').click(function () {
        //    location.href = '/index.php?g=ApplibWap&m=Find&a=calculatormore&' + $.param(formValue);
        //});
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
        
    return {
        init: init,
        calculate: calculate
    }
}();

calculator.init();