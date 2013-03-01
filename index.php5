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
            var loadingBox = $("#topBar .loading");
            var errorTxt = "出现异常";
            var loadingTxt = "处理中...";
            var currentVolumeNode = null;
            var currentChapterNode = null;

            var url = "/app/io.php5";
            var queryVolumesAction = "query_volumes";
            var queryChaptersAction = "query_chapters";
            var querySectionsAction = "query_sections";
            var queryContentAction = "query_content";

            var volume = null;
            var chapter = null;
            var section_start = null;
            var section_end = null;
            var content = '';

            var queryChaptersIO = null;//查询当前书卷下所有章数的io对象
            var querySectionsIO = null;

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

            /* 查询所有书卷 */
            function queryVolumes(){
                $.ajax(url, {
                    data: "action="+queryVolumesAction,
                    dataType: "json",
                    success: function (data){
                        if(data && data.length >= 1){
                            queryVolumesSuccess(data);
                        }else{
                            loadingBox.show().html("没有书卷数据");
                        }
                    },
                    error: error
                });
            }
            function queryVolumesSuccess(data){
                $('#dataList').get(0) && $('#dataList').remove();
                $('#prepare').get(0) && $('#prepare').fadeOut(200);
                var html = "<ul id=\"dataList\">";
                for(var i=0; i<data.length; i++){
                    var volume = data[i];
                    html += "<li id="+volume.Book+" data-alias="+volume.Alias+"><span>"+volume.BookTitle+"</span></li>";
                }
                html += "</ul>";
                $('body').append(html);
                bindUItoVolumes();
                loadingBox.hide();
            }
            function bindUItoVolumes(){
                $("#dataList span").click(function (e){
                    currentVolumeNode = $(e.currentTarget).parent();
                    queryChapters(currentVolumeNode.attr("id"));
                });
            }
            queryVolumes();

            /* 查询当前书卷下的所有章数 */
            function queryChapters(volumeId){
                if(!volumeId){return;}
                loadingBox.show().html(loadingTxt);
                volume = volumeId;
                queryChaptersIO && queryChaptersIO.abort();
                queryChaptersIO = $.ajax(url, {
                    data: "action="+queryChaptersAction+"&volume="+volume,
                    error: error,
                    success: function (data){
                        if(data && data > 0){
                            queryChaptersSuccess(data);
                        }else{
                            loadingBox.show().html(data);
                        }
                    }
                });
            }
            function queryChaptersSuccess(data){
                var html = "<dl class=\"fixFloat\">";
                for(var i=1; i<=data; i++){
                    html += "<dd><i>"+i+"</i></dd>";
                }
                html += "</dl>";
                if(currentVolumeNode.find("dl.fixFloat").get(0)){
                    currentVolumeNode.find("dl.fixFloat").remove();
                }
                currentVolumeNode.append(html);
                bindUItoChapters(currentVolumeNode);
                loadingBox.hide();
            }
            function bindUItoChapters(currentVolumeNode){
                currentVolumeNode.find('.fixFloat dd i').click(function (e){
                    currentVolumeNode.find('.fixFloat dd').removeClass("current");
                    currentChapterNode = $(e.currentTarget).parent();
                    currentChapterNode.addClass("current");
                    chapter = $(e.currentTarget).html();
                    querySections();
                });
            }

            /* 查询当前书卷下当前章数下的所有节数 */
            function querySections(){
                if(!volume || !chapter){return;}
                loadingBox.show().html(loadingTxt);
                querySectionsIO && querySectionsIO.abort();
                querySectionsIO = $.ajax(url, {
                    data: "action="+querySectionsAction+"&volume="+volume+"&chapter="+chapter,
                    error: error,
                    success: function (data){
                        if(data && data > 0){
                            querySectionsSuccess(data);
                        }else{
                            loadingBox.show().html(data);
                        }
                    }
                });
            }
            function querySectionsSuccess(data){
                var html = "<div class=\"fixFloat\">";
                for(var i=1; i<=data; i++){
                    html += "<s>"+chapter+":"+i+"</s>";
                }
                html += "</div>";
                if(currentChapterNode.find("div.fixFloat").get(0)){
                    currentChapterNode.find("div.fixFloat").remove();
                }
                currentChapterNode.append(html);
                loadingBox.hide();
            }


            function error(){
                loadingBox.show().html(errorTxt);
            }
        });
    </script>
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
