<?php
function dump($var){
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function val($name, $val){
    echo "<div class = 'val'><span>$name:</span>";
    if(is_array($val) || is_object($val)){
        dump($val);
    }else{
        echo "<span>$val</span>";
    }
    echo "</div>";
};

//This should rather go to php.ini
ini_set('display_errors', 1);
error_reporting(-1);

if(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== ''){
    //version for .htaccess with  RewriteRule ^(.*)$ index.php/$1 [QSA,L]
    $request = $_SERVER['PATH_INFO'];
    
}else{
    $request = $_SERVER['REQUEST_URI'];
}

//removing leading slash, if exists
$request = ltrim($request, '/');
//getting only what's before ? (uri query string) if it exists 
$pos = strpos($request, '?');
if ($pos !== false){
    $request = substr($request, 0, $pos);
} 

$requestArray     = explode('/', $request);
val('request',$request);
val('requestArray',$requestArray);

echo '<h3>Final parsing result</h3>';
val('controller',$requestArray[0] ?? 'undefined');
val('method',$requestArray[1] ?? 'undefined');
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

