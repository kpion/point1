<?php
/*
A simple single entry point (front controller) implementation.
More: https://github.com/kpion/point1
*/

//This should rather go to php.ini
ini_set('display_errors', 1);
error_reporting(-1);

/////////////////
////settings

//directory with our 'pages' files, to be included. Only files in this directory
//will eventually be loaded. Anything else will bring 404; Leading and trailing slashes required.
$allowedInclusionDirectory = __DIR__ . '/pages/';

//if nothing is passed in the url:
$defaultFile = 'home.php';

//USE_URI_PROTOCOL (environment variable) might be set e.g. in htaccess, if it is, we'll use it: 
$uriProtocol = getenv('USE_URI_PROTOCOL')?:'REQUEST_URI';
$request = isset($_SERVER[$uriProtocol])?$_SERVER[$uriProtocol]:'';

/////////////////
//processing the request

//removing  slashes
$request = ltrim($request, '/');

//getting rid of ? (uri query string)
$pos = strpos($request, '?');
if ($pos !== false){
    $request = substr($request, 0, $pos);
} 

$requestArray  = explode('/', $request);

$file = empty($requestArray[0]) ? $defaultFile : $requestArray[0] . '.php';

$fileAbsolute = realpath($allowedInclusionDirectory. $file);

//file not found, or is outside allowed directory.
//https://www.owasp.org/index.php/Testing_for_Local_File_Inclusion
if($fileAbsolute === false || strpos($fileAbsolute,  $allowedInclusionDirectory) !== 0){
    http_response_code(404);
    //forcing this file:
    $fileAbsolute =  $allowedInclusionDirectory . '404.php';
}

//we could do this:
//require_once $fileAbsolute;
//but we want to include some header/footer as well, so we'll need to complicate it a bit:

ob_start();
require $fileAbsolute;
$content = ob_get_clean();

//the layout needs to output $content somewhere:
require $allowedInclusionDirectory . 'layouts/default.php';

