<?php
ini_set('display_errors', 1);
error_reporting(-1);
function dump($var){
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function val($name, $val, $help = ''){
    echo "<div class = 'val'><span title = '$help'>$name:</span>";
    if(is_array($val) || is_object($val)){
        dump($val);
    }else{
        echo "<span>$val</span>";
    }
    echo "</div>";
};

//chyba nie działa gdy http://localhost:82/point/point1/point/point1
//czyli gdy controller/method jest... takie samo jak katalog
function request_uri() {
    $uri = '';
    

    if (empty($_SERVER['PATH_INFO']) === false) {
        $uri = $_SERVER['PATH_INFO'];
    }else{
        if (isset($_SERVER['REQUEST_URI']) === true) {
            $scheme = empty($_SERVER['HTTPS']) === true || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https';
            $uri = parse_url($scheme.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }elseif (isset($_SERVER['PHP_SELF']) === true) {
            $uri = $_SERVER['PHP_SELF'];
        }else{
            return "";
        }
        $request_uri = isset($_SERVER['REQUEST_URI']) === true ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
        $script_name = $_SERVER['SCRIPT_NAME'];
        $base_uri = strpos($request_uri, $script_name) === 0 ? $script_name : str_replace('\\', '/', dirname($script_name));
        $base_uri = rtrim($base_uri, '/');
        if ($base_uri !== '' && strpos($uri, $base_uri) === 0) {
            $uri = substr($uri, strlen($base_uri));
        }
        return '/' . ltrim($uri, '/');
        
    }
    return $uri;
}


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='UTF-8'>
        <title>single entry point tests</title>
        <!--
        <link rel='stylesheet' type='text/css' href='assets/styles/normalize.css'>
        <script type='text/javascript' src='assets/scripts/jquery.js'></script>
        -->
        <style>
            body {
                margin: 0 auto;
                max-width: 60em;
            }

            .val span:nth-child(1){
                font-weight:bold;
                min-width: 250px;
                display: inline-block;
            }

            .msg{
                min-width: 100%;
                border:1px solid #aaa;
                box-sizing: border-box;
            }
            .msg{
                min-width: 100%;
                border:1px solid #aaa;
                box-sizing: border-box;
                padding:15px;
            }
            .msg .title{
                font-weight:bold;
                font-size:110%;
                border-bottom:1px dotted #ccc;
            }

            .msg div:not(.title){
                padding:10px;
            }

            .msg div.apache{
                background-color:#711;
                color:#fff;
            }
            .msg div.nginx{
                background-color:#117;
                color:#fff;
            }
            section{
                border:1px dotted #ccc;
                padding:10px;
                margin-top:20px;
                
                min-width:100%;
                box-sizing: border-box;
            }
            .result{
                background-color: #f5ffee;
            }
            
        </style>
    </head>
    <body>
        <h1>Single entry point</h1>
        
        <?php


        echo '<section>';
        //displaying a msg about what webserver we are served by:
        $serverInfo = '';
        $webserver = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $serverInfo .= $webserver;

        $msgClass = '';
        if(stripos($webserver,'apache') !== false){
            $msgClass = 'apache';
        };

        if(stripos($webserver,'nginx') !== false){
            $msgClass = 'nginx';
        };
        
        if(($_SERVER['KPD-HTACCESS'] ?? false) === 'true'){
            $serverInfo .= '<br>With .htaccess enabled and running';
        }
        
        echo "<div class = 'msg'><div class = 'title'>SERVER SOFTWARE</div><div class = '$msgClass'>$serverInfo</div></div>";

        echo "<h2>Some \$_SERVER vars</h2>";
        foreach (['DOCUMENT_ROOT', 'DOCUMENT_URI', 'REQUEST_URI', 'QUERY_STRING', 'PATH_INFO' ] as $name) {
            val($name, $_SERVER[$name] ?? 'Null or undefined');
        }



        echo "<h2>Some other important stuff</h2>";
        val('__DIR__', __DIR__);
        $docAfterRoot = str_replace($_SERVER['DOCUMENT_ROOT'],'', __DIR__);
        val('__DIR__ after root', $docAfterRoot, "{$_SERVER['DOCUMENT_ROOT']} removed from __DIR__");
        $requestTrick = str_replace($docAfterRoot,'',$_SERVER['REQUEST_URI']);
        val('$requestTrick', $requestTrick, "Above removed from request uri: {$_SERVER['REQUEST_URI']}");
        echo '</section>';

        /////////////////////////////////////////////////////////////////////////////////////////////
        
        echo "<section class = 'result'>";
        echo "<h2>Parsing method  I</h2>";

        //USE_URI_PROTOCOL is an enrironment var which can be set by .htaccess
        $uriProtocol = getenv('USE_URI_PROTOCOL')?:'REQUEST_URI';
        $request = isset($_SERVER[$uriProtocol])?$_SERVER[$uriProtocol]:'';
        val('request original',$request);

        //getting only what's before ? (uri query string) if it exists 
        if (($pos = strpos($request, '?')) !== false){
            $request = substr($request, 0, $pos);
        } 

        //trimming slashes
        $request = trim($request, '/');
        $requestArray  = empty($request)? [] : explode('/', $request);

        val('request',$request);
        val('requestArray',$requestArray);

        echo '<h3>Final parsing result</h3>';
        val('controller',$requestArray[0] ?? 'undefined');
        val('method',$requestArray[1] ?? 'undefined');

        echo '</section>';

        /////////////////////////////////////////////////////////////////////////////////////////////

        echo "<section class = 'result'>";
        echo "<h2>Parsing method  II</h2>";

        $request = request_uri();

        $requestArray     = explode('/', $request);
        val('request',$request);
        val('requestArray',$requestArray);

        echo '<h3>Final parsing result</h3>';
        val('controller',$requestArray[0] ?? 'undefined');
        val('method',$requestArray[1] ?? 'undefined');

        echo '</section>';


        echo '<section>';
        //file: index.php
        //assuming this file is in '/project/index.php' and then we have some subdirs like 'content' and 'blah', then

        echo "<h3>Checking if including the *file* is safe</h3>"; 
        $filePathRelative = 'someFile.php';//that comes from user, like from e.g.  $_GET['file']
        
        $filePathAbsolute = realpath(__DIR__.'/'. $filePathRelative);
        


        if($filePathAbsolute === false){
            echo "does not exist....\n"; 
        }

        //the file exists, but can we really include the file?
        if(strpos($filePathAbsolute, __DIR__ . '/content') !== 0 && strpos($filePathAbsolute, __DIR__ . '/blah') !== 0){
            echo 'file path not allowed';
        }

        //we can safely include/require/whatever the $filePathAbsolute
        echo "</section>";//end of .result

        ?>

       

        <hr style = 'margin:50px 10px;'>

        
        <?php
        echo '<section>';
        echo "<h2>All the possible stuff</h2>";

        echo '<h3>$_GET</h3>';
        dump($_GET);

        echo '<h3>$_POST</h3>';
        dump($_POST);

        echo '<h3>$_SERVER</h3>';
        dump($_SERVER);
        echo '</section>';

        ?>
     
    </body>
</html>
