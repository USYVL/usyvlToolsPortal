<?php
ini_set('display_errors',true);
$toprel = "/inc";
// this should reflect where this file is in relationship to the top level dir
// ie: shaving this bit off the end of __DIR__ should give us the top level dir
// revising this info on 2023-06-11.  
//   With recent restructuring everything should be relative to the public_html 
//   subdir - which should be consistent across all the new code organization.  
//   especially if we use realpath to resolve stuff.  So the cases we want to cover are:
//     site is top level of a vhost: scheduling.usyvl.org/
//     site is subdir of a vhost:  tools.usyvl.org/scheduling
//     site is subdir of a generic site: ??
//   doesn't really matter how deep it is in a given filesystem
//   using realpath on the various elements should avoid issues with symlinks
//   NOTE: PHP_SELF is relative, so can't be realpath'ed the same way that SCRIPT_FILENAME can be
//   case insensitive filesystems can be problematic as well Sites vs sites in macos

// need to use strtolower for this comparison of dirs, but not for the actual path which may (or may not) be case sensitive
$top = preg_replace(":{$toprel}$:",'',realpath(__DIR__));                     // this is being done in init.php, so the dir is the dir above /inc
$basepath = strtolower($top);                                                 // this is being done in init.php, so the dir is the dir above /inc
$thispath = strtolower(realpath(dirname($_SERVER['SCRIPT_FILENAME'])));       // this is dir that the calling script is in
$diffpath = str_replace($basepath,'',$thispath);
$dlevel   = count(explode("/",$diffpath))-1;
$aa = ($dlevel > 0) ? array_fill(0,$dlevel,"..") : array(".");
$rel = implode("/",$aa) . "/";
// now need to get rid of the common prefix

// There is some code left for debugging this area at bottom of the file
// This was for debugging
// $dbufa = array(
//     '__DIR__'               => __DIR__,
//     '__DIR__ (rp)'          => realpath(__DIR__),
//     '__FILE__'              => __FILE__,
//     '__FILE__ (rp)'         => realpath(__FILE__),
//     'DOCUMENT_ROOT'         => $_SERVER['DOCUMENT_ROOT'],
//     'DOCUMENT_ROOT (rp)'    => realpath($_SERVER['DOCUMENT_ROOT']),
//     'SCRIPT_FILENAME'       => $_SERVER['SCRIPT_FILENAME'],
//     'SCRIPT_FILENAME (rp)'  => realpath($_SERVER['SCRIPT_FILENAME']),
//     'PHP_SELF'              => $_SERVER['PHP_SELF'],
//     'toprel (src code)'     => "$toprel",
//     'top'                   => "$top",
//     'dlevel'                => "$dlevel",
//     'rel'                   => "$rel",
//     'basepath'              => $basepath,
//     'thispath'              => $thispath,
//     'diffpath'              => $diffpath,
//     'difflevel'             => $dlevel
// );
// $dbuf = "<table>";
// foreach( $dbufa as $k => $v){
//     $dbuf .= "<tr>";
//     $dbuf .= "<td>$k</td>";
//     $dbuf .= "<td>$v</td>";
//     $dbuf .= "</tr>";
// }
// $dbuf .="</table>\n";
// print "$dbuf";


$incdirs = array('','inc');  // dirs to include below the top level dir
foreach($incdirs as $inc){
    ini_set('include_path',ini_get('include_path') . ":$top/" . $inc);
}

require_once 'errPageClass.php';

define('CONFIG_FILE',"config.php");

// check for config file
$configLocation = stream_resolve_include_path(CONFIG_FILE);
// echo "incpath: " . ini_get('include_path') . "<br>configLocation:" . $configLocation . "<br>\n";
if (file_exists($configLocation)){
    require_once $configLocation;
}
else{
    $h = (new errPage(array('title'=>"USYVL Tools Portal",'body'=>"No config.php file found - copy config.php.default to config.php and then modify values appropriately")))->run();
}

if (! defined('LOCAL_INCLUDE_PATHS')){
    $h = (new errPage(array('title'=>"USYVL Tools Portal",'body'=>"LOCAL_INCLUDE_PATHS undefined in config file (". CONFIG_FILE . ")")))->run();
}

$ipaths = explode(PATH_SEPARATOR,LOCAL_INCLUDE_PATHS);
foreach($ipaths as $ipc){
    if ( ! is_dir($ipc)){
        $h = (new errPage(array('title'=>"USYVL Tools Portal",'body'=>"A path ($ipc) in LOCAL_INCLUDE_PATHS does not exist.  Modify config file (". CONFIG_FILE . ")")))->run();
    }
}
set_include_path(get_include_path() . PATH_SEPARATOR . LOCAL_INCLUDE_PATHS);

if (! stream_resolve_include_path('htmlDoc.php')){
    $gip = get_include_path();
    $h = (new errPage(array('title'=>"USYVL Tools Portal",'body'=>"Unable to find htmlDoc.php in the current include path ($gip)")))->run();

}

require_once 'versionInfoGit.php';
require_once 'htmlDoc.php';
require_once 'fileUtils.php';
require_once 'printUtils.php';


?>