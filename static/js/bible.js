/**
 * Created with JetBrains PhpStorm.
 * User: qizhuq
 * Date: 3/2/13
 * Time: 10:25 AM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function (){
    var prepared = false;//第一层数据是否准备好了
    var loadingBox = $("#topBar .loading");
    var errorTxt = "出现异常";
    var loadingTxt = "处理中...";
    var currentVolumeNode = null;
    var currentChapterNode = null;
    var currentSectionNode = null;

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
    var queryContentIO = null;

    (function (){
        $(window).scroll(function (){
            var top = $(window).scrollTop();
            var topBar = $("#topBar");
            if(top >= topBar.height()/2){
                topBar.css({
                    position: "absolute",
                    left: 0,
                    width: "100%",
                    top: top,
                    zIndex: 2,
                });
            }else{
                topBar.css({
                    position: "relative",
                    width: "auto",
                    top: 0
                });
            }
        });
    })();

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
            html += "<li id="+volume.Book+" data-alias="+volume.Alias+"><span class=\"fixFloat\">"+volume.BookTitle+"</span></li>";
        }
        html += "</ul>";
        $('body').append(html);
        bindUItoVolumes();
        loadingBox.hide();
        prepared = true;
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
            html += "<s id="+i+">"+chapter+":"+i+"</s>";
        }
        html += "</div>";
        if(currentChapterNode.find("div.fixFloat").get(0)){
            currentChapterNode.find("div.fixFloat").remove();
        }
        currentChapterNode.append(html);
        bindUItoSections();
        loadingBox.hide();
    }
    function bindUItoSections(){
        currentChapterNode.find("div s").click(function (e){
            currentSectionNode = $(e.currentTarget);
            section_start = currentSectionNode.attr("id");
            queryContent();
        });
    }

    /* 查询具体的经文内容 */
    function queryContent(){
        if(!section_start){
            return;
        }
        if(!section_end){
            section_end = section_start;
        }
        if(section_end < section_start){
            return ;
        }
        queryContentIO && queryContentIO.abort();
        queryContentIO = $.ajax(url, {
            data: "action="+queryContentAction+"&volume="+volume+"&chapter="+chapter+"&section_start="+section_start+
            "&section_end="+section_end,
            dataType: "json",
            error: error,
            success: function (data){
                if(data && toString.call(data) === "[object Object]"){
                    data
                    if(currentChapterNode.find("div").find("p").get(0)){
                        currentChapterNode.find("div").find("p").remove();
                    }
                    currentChapterNode.find("div").append("<p>"+data.TextData+"</p>");
                    var top = currentSectionNode.offset().top;
                    $(document).scrollTop(top);
                    section_end = null;
                    section_start = null;
                }
            }
        });
    }


    function error(){
        loadingBox.show().html(errorTxt);
    }
});
