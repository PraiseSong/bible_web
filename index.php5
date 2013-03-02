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
    <script type="text/javascript" src="static/js/bible.js"></script>
</head>
<body>
    <div id="topBar">
        <div class="content fixFloat">
            <img src="static/images/logo.png" alt="中文版在线圣经" class="logo" />
            <span class="search">快速找经文</span>
            <span class="loading">处理中...</span>
        </div>
    </div>

    <ul id="dataList">
        <li>
            <span>创世纪</span>
            <dl class="fixFloat">
                <dd>
                    <i>1</i>
                </dd>
                <dd class="current">
                    <i>2</i>
                    <div class="fixFloat">
                        <s>2:1</s>
                        <s>2:2</s>
                        <p>地是空虚混沌。渊面黑暗。神的灵运行在水面上。</p>
                    </div>
                </dd>
            </dl>
        </li>
        <li>
            <span>出埃记</span>
        </li>
    </ul>

    <div id="prepare">
        <img src="static/images/logo.png" alt="中文版在线圣经" class="logo" />
        <img src="static/images/ajax-loader.gif" alt="中文版在线圣经正在加载圣经数据" />
    </div>
</body>
</html>
