<?php
/**
 * Created by JetBrains PhpStorm.
 * User: qizhuq
 * Date: 3/1/13
 * Time: 8:41 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * 默认打开php的所有报错消息
 */
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once("../funs/db.php5");

$db_server = "localhost";
$db_user = "root";
$db_password = "ZHUqi@159";
$base = "bible";
$table = "crossbible";
$db = new DB($db_server, $db_user, $db_password, $base);
$db->query("SET NAMES 'UTF8'");

$action = @$_GET["action"];
switch($action){
    case "query_volumes":
        query_volumes();
        break;
    case "query_chapters":
        query_chapters();
        break;
    case "query_sections":
        query_sections();
        break;
    case "query_content":
        query_content();
        break;
}

/**
 * 查询所有书卷
 * @return json_encode
 */
function query_volumes(){
    global $db,$table;
    $volumes = array();

    $resource = $db -> query("select distinct Alias,Book,BookTitle from $table");
    while($data = $db -> fetchNextObject($resource)){
        array_push($volumes, $data);
    }

    echo json_encode($volumes);
}

/**
 * 查询当前书卷下面所有章数
 * @return number
 */
function query_chapters(){
    global $db,$table;
    $volume = @$_GET["volume"];
    $chapters = array();
    $tem_chapters = array(0);

    if(!isset($volume)){
        echo "没有提交书卷的id";
    }else{
        $resource = $db -> query("select distinct Verse from $table where Book=$volume");
        while($data = $db -> fetchNextObject($resource)){
            array_push($chapters, $data);
        }
        foreach($chapters as $key=>$chapter){
            $verse = preg_split("/:/", $chapter->Verse);
            if(!in_array($verse[0], $tem_chapters)){
              array_push($tem_chapters, $verse[0]);
            }
        }
        echo max($tem_chapters);
    }
}

/**
 * 查询当前书卷下，当前章数下的所有节数
 * @return number
 */
function query_sections(){
    global $db,$table;
    $volume = @$_GET["volume"];
    $chapter = @$_GET["chapter"];
    $sections = array();
    $tem_sections = array(0);

    if(!isset($volume)){
        echo "没有提交书卷的id";
    }else if(!isset($chapter)){
        echo "没有提交章数";
    }else{
        $resource = $db -> query("select distinct Verse from $table where Book=$volume");
        while($data = $db -> fetchNextObject($resource)){
            array_push($sections, $data);
        }
        foreach($sections as $key=>$section){
            $verse = preg_split("/:/", $section->Verse);
            if($verse[0] === $chapter){
                array_push($tem_sections, $verse[1]);
            }
        }
        echo max($tem_sections);
    }
}

/**
 * 查询具体的经文
 */
function query_content(){
    global $db,$table;
    $volume = @$_GET["volume"];
    $chapter = @$_GET["chapter"];
    $section_start = @$_GET["section_start"];
    $section_end = @$_GET["section_end"];
    $verse = null;

    if(!isset($volume)){
        echo "没有提交书卷的id";
    }else if(!isset($chapter)){
        echo "没有提交章数";
    }else if(!isset($section_start) || !isset($section_end)){
        echo "没有提交正确的节数";
    }else{
        $verse = $chapter.":".$section_start;
        $resource = $db -> queryUniqueObject("select TextData from $table where Book=$volume and Verse='$verse'");
        echo json_encode($resource);
    }
}
?>