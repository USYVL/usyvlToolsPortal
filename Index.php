<?php
// This requires access to some shared external resources, so we need to do some 
// error checking for that.
define('CONFIG_FILE',"./config.php");

class errPage {
    function __construct($a=array('body' => "Default Body Text",'title' => "USYVL Tools Portal")){
        $this->data = $a;
    }
    function run(){
        $buf  = "<!doctype html>\n<html lang=\"en\">\n<head\n";
        $buf .= "  <meta charset=\"utf-8\">\n";
        $buf .= "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
        $buf .= "  <title>{$this->data['title']}</title>\n";
        $buf .= "  <meta name=\"description\" content=\"USYVL Tools Portal\">\n";
        $buf .= "  <meta name=\"author\" content=\"USYVL\">\n";
        $buf .= "</head>\n\n";
        $buf .= "<body>\n{$this->data['body']}\n</body>\n</html>\n";
        print "$buf";
        exit();
    }
}
// check for config file
if (file_exists(CONFIG_FILE)){
    include_once CONFIG_FILE;
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
require_once('htmlDoc.php');
require_once('fileUtils.php');
require_once('printUtils.php');

class usyvlUtilsIndex {
    function __construct(){
        $this->server = $_SERVER['SERVER_NAME'];
        $this->requri = $_SERVER['REQUEST_URI'];
        $this->dir = dirname($_SERVER['SCRIPT_NAME']);
        $this->local = 'http://' . $this->server . $this->dir;
        $this->localS = 'https://' . $this->server . $this->dir;
        $this->buf = '';

        // start this list off with files/dirs we do NOT want to show up at the bottom (optional)
        $this->specified = array('.git','.gitignore','README.md','Index.php','usyvllogo.jpg','unused','testing','krumo-1.6','wiki-db','.DS_Store');
        $this->fileList = array();
        // scan local dir for directories and files
        // add in the specific ones we want to include
        // display any additional items found
        $this->fileList = scandir_gen(".",false);

        $this->knownEntries();

        // once the primary, knownn entries are checked and possibly accumulated in the buffer,
        // lets see if there are others to display
        $this->remainingEntries();

    }
    function remainingEntries(){
        $this->others = array_diff($this->fileList,$this->specified);
        //print_pre($this->fileList,"filelist");
        //print_pre($others,"others");

        if ( count($this->others) == 0) return;
        $this->newSection('Additional unspecified directories existing in this folder:');
        foreach($this->others as $o){
            if (is_link($o)) continue;

            $desc = "Unknown folder (consider adding README.md)";
            //print "other: $o<br>\n";
            if (file_exists($o . "/README.md")) {
                $lines = file($o . "/README.md");
                $desc = $lines[0];
            }
            $this->addEntry($o,"$o",$desc);
        }
    }
    function knownEntries(){
        $this->newSection('Local Copy - Scheduling - backups, sandboxes, etc... (copy is specific to this dev or hosting):');
        $this->addEntry("scheduling","USYVL Schedule Building Utilities (Current revision)","Tools to maintain the Game Schedules portion of the website.");
        $this->addEntry("previous-sched","USYVL Schedule Building Utilities (Older revision)","Backup copy (not completely up-to-date) in case the most current revision has unforseen issues.");
        $this->addEntry("sandbox-sched","USYVL Schedule Building Utilities (Sandbox copy)","Sandbox version - file xfers have different destinations.");

        $this->addEntry("donate","USYVL Donorlist Builder","Tools to create and maintain the donorlist image on the Donate page.");
        $this->addEntry("workflow","USYVL workflow","Workflow system to manage sites over the course of a season.");
        $this->addEntry("mobile","USYVL mobile","Mobile version providing access to schedules.");
        $this->addEntry("youthvb","Youthvolleyball.com","Youthvb code.");
        $this->addEntry("mwf","Mobile Web Framework","MWF code.");

        $this->newSection('Absolute references - (do not change based on dev or hosting platform):');
        $this->addEntry("http://www.usyvl.org","USYVL Home Page","USYVL Live Public Server");
        $this->addEntry("http://m.usyvl.org","Mobile Site (live)","USYVL Live Mobile Site");
        $this->addEntry("http://schedules.usyvl.org","Schedules Site (live)","USYVL Live Schedules Site");
        $this->addEntry("http://youthvolleyball.com","YouthVolleyball.com (live)","YouthVolleyball.com site");
        $this->addEntry("http://venom.eri.ucsb.edu/aaron/usyvl/wiki","Wiki for USYVL software dev","USYVL development/deployment wiki");
    }
    // Trying to sort out if we want to group absolute references (ie: URLs) together
    // programmatically, or require input to do so.  Also, do we want to differentiate
    // them in the output (ie: a separate section)
    function newSection($entry){
        $this->buf .= '<h3>' . $entry . '</h3>';
        $this->buf .= "\n";
    }
    // Do we want to try to track the ones that are skipped?  To output below the main list as available
    function addEntry($entry,$label,$desc,$ageFrom = ''){
        $exists = file_exists($entry);
        $absurl   = preg_match('/^https*:\/\//',$entry);

        if (! $exists && ! $absurl ) return;   // skip any non-existent entry

        // could compare entry to this->local, if they are the same, then we have a URL that matches.
        // indicating that this copy, is the absolute URL referenced.
        // possibly indicate that situation in the link somehow, not sure exactly
        // how or what we want to do and if there is a real significance in it.
        // only really applicable to the wiki it looks like.
        $indicator = "";
        if ($absurl){
            if ( $this->local == dirname($entry) || $this->localS == dirname($entry) ){
                // the server is the same
                $indicator = " #";

                // since the entry specified is on the local server see if the basename
                // portion matches a directory at this location, if so tag it so it doesn't
                // show up in the other section.
                $bn = basename($entry);
                if( file_exists($bn) && is_dir($bn)){
                    $this->specified[] = $bn;
                }
            }
        }

        $this->specified[] = $entry;
        $this->buf .= '<a href="' . $entry . '">' . $label . '</a> - ' . $desc ;
        $this->buf .= "$indicator<br />\n";
    }
    function output(){
        return $this->buf;
    }
}
$h = new htmlDoc("USYVL Tools Portal","");
$h->setHeading();

$h->addStyle("body { font-family: arial,verdana,helvetica; font-size: 10pt; }");
$h->addStyle("td { padding: 0px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 14; color: 000000; border: 0;}");
$h->addStyle("table { border-collapse: collapse;}");
$h->addStyle(".banner { padding-left: 10px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 12; color: 0000ff; border: 0;}");
$h->addStyle(".rcell { padding-left: 10px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 12; color: 0000ff; border: 0;}");
$h->addStyle("h1 { padding: 0px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 20pt; font-style: bold; color: 000000; border: 0;}");
$h->addStyle("h2 { padding: 0px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 16pt; font-style: bold; color: 000000; border: 0;}");
$h->addStyle("h3 { padding: 0px; padding-top: 5px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 12pt; font-style: bold; color: 000000; border: 0;}");
$h->addStyle("a { padding: 0px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; color: 000000; border: 0;}");
$h->addStyle("div.version {
    padding: 0px;
    margin: 0px;
    margin-bottom: 5px;
    vertical-align: top;
    font-family: helvetica;
    font-size: 12pt;
    font-weight: bold;
    color: #aa0000;
    border: 0;
    display: inline-block;
}"); 
$h->addStyle("div.installation {
    padding: 0px;
    margin: 0px;
    margin-left: 0.5em;
    margin-bottom: 5px;
    vertical-align: top;
    font-family: helvetica;
    font-size: 12pt;
    font-weight: bold;
    color: #0000aa;
    border: 0;
    display: inline-block;
}"); 

$h->beg();

$version = "Placeholder";
$installation_nickname = INSTALLATION_NICKNAME;
$uui = new usyvlUtilsIndex();
// this should be rejiggered using divs...
print <<<EOF
<table border=0 width='100%'>
<tr>
<td style='padding: 0px; width: 80px; '>
<img src="usyvllogo.jpg" align=left alt='USYVL Logo'><br>
</td>
<td class='banner'>
<h1>USYVL Tools Portal</h1>
<div class="version">$version</div> on <div class="installation">$installation_nickname</div>
<p>
These following sites provide tools for maintaining specific aspects of the
USYVL website.
</p>
</td>
</tr>
</table>
<br>
EOF;

//$h->a_br("scheduling","USYVL Schedule Building Utilities (Current revision)","Tools to maintain the Game Schedules portion of the website.");
//$h->a_br("previous-sched","USYVL Schedule Building Utilities (Older revision)","Backup copy (not completely up-to-date) in case the most current revision has unforseen issues.");
//$h->a_br("sandbox-sched","USYVL Schedule Building Utilities (Sandbox copy)","Sandbox version - file xfers have different destinations.");
//$h->a_br("donate","USYVL Donorlist Builder","Tools to create and maintain the donorlist image on the Donate page.");
//$h->a_br("workflow","USYVL workflow","Workflow system to manage sites over the course of a season.");
//$h->a_br("mobile","USYVL mobile","Mobile version providing access to schedules.");
//$h->a_br("http://www.usyvl.org","USYVL Home Page","USYVL Live Public Server");
//$h->a_br("wiki","Wiki for USYVL software development/deployment","USYVL development wiki");
//
//print "==========================<br>\n";
print $uui->output();


$h->end();

//phpinfo();

?>
