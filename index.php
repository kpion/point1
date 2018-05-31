<?php
/*
A simple single point of entry (front controller) implementation.
More: https://github.com/kpion/point1
*/

//This should rather go to php.ini
ini_set('display_errors', 1);
error_reporting(-1);

/////////////////
////Settings. 

//Used to build links, load css / js etc. With trailing slash. 'auto' means we'll try to make it automatically (much below).
$baseUrl = 'auto';

//file system root.
$appDir = __DIR__;

//directory with our 'page' files, to be included. Only files in this directory
//will eventually be loaded. Anything else will bring 404; Leading and trailing slashes required.
$pagesDir = __DIR__ . '/pages/';

//if nothing is passed in the url (here we don't add any file extension):
$defaultPage = 'home';

//USE_URI_PROTOCOL (environment variable) might be set e.g. in htaccess, if it is, we'll use it: 
$uriProtocol = getenv('USE_URI_PROTOCOL')?:'REQUEST_URI';
$request = isset($_SERVER[$uriProtocol])?$_SERVER[$uriProtocol]:'';

/////////////////
//Auto resolving baseUrl

if($baseUrl === 'auto'){
    function getBaseUrl($request){
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http'; 
        $port = $_SERVER['SERVER_PORT'];
        $port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";
        //only necessary in case of .htaccess and multiple projects under one doc root:
        $baseRequestUri = htmlspecialchars(str_replace($request, '', $_SERVER['REQUEST_URI']));
        $baseUrl = "{$protocol}://{$_SERVER['SERVER_NAME']}{$port}{$baseRequestUri}";
        return  $baseUrl;  
    }    
    $baseUrl = trim(getBaseUrl($request),'/') . '/';
}

/////////////////
//Processing the request

//removing  slashes
$request = ltrim($request, '/');

//getting rid of query string (everything after ?)
$pos = strpos($request, '?');
if ($pos !== false){
    $request = substr($request, 0, $pos);
} 

$requestArray  = explode('/', $request);

//this will be the requested file, like home.php or contact.php
$page = empty($requestArray[0]) ? $defaultPage : $requestArray[0];
$file = $page . '.php';

$fileAbsolute = realpath($pagesDir . $file);

//file not found, or is outside allowed directory.
//https://www.owasp.org/index.php/Testing_for_Local_File_Inclusion
if($fileAbsolute === false || strpos($fileAbsolute,  $pagesDir) !== 0){
    http_response_code(404);
    //forcing this file:
    $fileAbsolute =  $pagesDir . '404.php';
}

//we could do this:
//require_once $fileAbsolute;
//but we want to include some header/footer as well, so we'll need to complicate it a bit:

ob_start();
require $fileAbsolute;
$content = ob_get_clean();

//the layout needs to output $content somewhere:
require $pagesDir . 'layouts/default.php';

