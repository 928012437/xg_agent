<?php
class CreditshopMobilePage extends PluginMobilePage 
{
    function __construct(){
        global $_W;
        parent::__construct();
        if($_COOKIE['openid']!=''){
            $_W['openid']=$_COOKIE['openid'];
        }
    }
}
?>