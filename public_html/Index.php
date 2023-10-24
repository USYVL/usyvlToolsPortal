<?php
// This requires access to some shared external resources, so we need to do some 
// error checking for that.
require_once './inc/init.php';
require_once 'usyvlUtilsIndex.php';

$h = new htmlDoc("USYVL Tools Portal","");
$h->setHeading();
$h->css('./css/usyvl-portal.css');
$h->beg();


// get version for the portal itself - timing of this is important, must be done AFTER usyvlUtilsIndex is called
// since that may load version numbers of subsites into GLOBALS['version']
if ( file_exists('version.php')){
    include './version.php';
    $version = $GLOBALS['version'];
}

$installation_nickname = INSTALLATION_NICKNAME;

print <<<EOF
<div id="banner-container">
<div id="logo-container"><img src="usyvllogo.jpg" align=left alt='USYVL Logo'><br></div><!-- end logo-container -->
<div id="text-container">
<h1>USYVL Tools Portal</h1>
<div class="version">version {$GLOBALS['gitVersionInfo']->version()}</div> deployed on <div class="installation">$installation_nickname</div>
<div class="version-info">{$GLOBALS['gitVersionInfo']->info()}</div>
</div><!-- end text-container -->
<h3>This portal provides links to various resources used to maintain and support USYVL programs</h3>
</div><!-- end banner-container -->
<br>
EOF;

$uui = new usyvlUtilsIndex();
print $uui->output();

$h->end();
?>
