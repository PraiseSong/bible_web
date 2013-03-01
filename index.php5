<?php
/**
 * Created by JetBrains PhpStorm.
 * User: qizhuq
 * Date: 3/1/13
 * Time: 12:52 PM
 * To change this template use File | Settings | File Templates.
 */

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>中文在线圣经</title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="email=no"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0;" name="viewport"/>
    <link rel='stylesheet' href='static/css/bible.css'/>
    <script type="text/javascript" src="static/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function (){
            var prepared = false;//第一层数据是否准备好了

            function adjustPreparePosition(){
                if(prepared){
                    $(window).unbind("resize.adjustPreparePosition");
                    return;
                }
                var winW = $(window).width(),
                    winH = $(window).height();

                var prepareBox = $('#prepare'),
                    prepareBoxH = prepareBox.outerHeight() < 100 ? 169 : prepareBox.outerHeight();

                $('#prepare').css({
                    marginTop: (winH - prepareBoxH) / 2
                });
            }
            adjustPreparePosition();
            $(window).bind("resize.adjustPreparePosition",adjustPreparePosition);
        });
    </script>
</head>
<body>
  <div id="prepare">
      <img src="static/images/logo.png" alt="中文版在线圣经" class="logo" />
      <img src="static/images/ajax-loader.gif" alt="中文版在线圣经正在加载圣经数据" />
  </div>
</body>
</html>
