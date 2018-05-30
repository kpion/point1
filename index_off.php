<?php
//This should rather go to php.ini
ini_set('display_errors', 1);
error_reporting(-1);

//USE_URI_PROTOCOL is an enrironment var which can be set by .htaccess
$uriProtocol = getenv('USE_URI_PROTOCOL')?:'REQUEST_URI';
$request = isset($_SERVER[$uriProtocol])?$_SERVER[$uriProtocol]:'';

//removing leading slash
$request = ltrim($request, '/');
//getting only what's before ? (uri query string)
$pos = strpos($request, '?');
if ($pos !== false){
    $request = substr($request, 0, $pos);
} 

$requestArray     = explode('/', $request);
val('controller',$requestArray[0] ?? 'undefined');
val('method',$requestArray[1] ?? 'undefined');

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

