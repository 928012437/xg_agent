<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
	<link rel="stylesheet" href="./src/style/font/iconfont.css">
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

    <title></title>
    <script>

      uniacid=getPar('i');
      uniacid=uniacid.replace(/[^0-9]/ig,"");
      tjid=getPar('tjid');
      if(tjid!=''){
        tjid=tjid.replace(/[^0-9]/ig,"");
      }
      page=0;
      type=1;
      sort='';
      id='';
      shownotice=true;
      urlstr="http://w.xghd.cc/app/index.php?i="+uniacid+"&c=entry&m=xg_agent&do=mobile&r=";

      function getPar(par){
        //获取当前URL
        var local_url = document.location.href;
        //获取要取得的get参数位置
        var get = local_url.indexOf(par +"=");
        if(get == -1){
          return false;
        }
        //截取字符串
        var get_par = local_url.slice(par.length + get + 1);
        //判断截取后的字符串是否还有其他get参数
        var nextPar = get_par.indexOf("&");
        if(nextPar != -1){
          get_par = get_par.slice(0, nextPar);
        }
        return get_par;
      }



    </script>
  </head>
  <body>
    <div id="app"></div>
    <script src="./dist/build.js"></script>
  </body>
</html>

        
        
        