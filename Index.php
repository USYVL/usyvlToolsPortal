<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/local/lib/site-php');

require_once('htmlDoc.php');
require_once('fileUtils.php');
require_once('printUtils.php');

class usyvlUtilsIndex {
    function __construct(){
        $this->server = $_SERVER['SERVER_NAME'];
        $this->requri = $_SERVER['REQUEST_URI'];
        $this->dir = dirname($_SERVER['REQUEST_URI']);
        $this->local = 'http://' . $this->server . $this->dir;
        $this->locals = 'https://' . $this->server . $this->dir;
        $this->buf = '';

        // start this list off with files/dirs we do NOT want to show up at the bottom (optional)
        $this->specified = array('.git','.gitignore','README.md','Index.php','usyvllogo.jpg','unused','testing','krumo-1.6');
        $this->fileList = array();
        // scan local dir for directories and files
        // add in the specific ones we want to include
        // display any additional items found
        $this->fileList = scandir_gen(".",false);

        $this->entries();
        $others = array_diff($this->fileList,$this->specified);
        //print_pre($this->fileList,"filelist");
        //print_pre($others,"others");
    }
    function entries(){
        $this->newSection('Local Copy - Scheduling - backups, sandboxes, etc...:');
        $this->addEntry("scheduling","USYVL Schedule Building Utilities (Current revision)","Tools to maintain the Game Schedules portion of the website.");
        $this->addEntry("previous-sched","USYVL Schedule Building Utilities (Older revision)","Backup copy (not completely up-to-date) in case the most current revision has unforseen issues.");
        $this->addEntry("sandbox-sched","USYVL Schedule Building Utilities (Sandbox copy)","Sandbox version - file xfers have different destinations.");

        $this->addEntry("donate","USYVL Donorlist Builder","Tools to create and maintain the donorlist image on the Donate page.");
        $this->addEntry("workflow","USYVL workflow","Workflow system to manage sites over the course of a season.");
        $this->addEntry("mobile","USYVL mobile","Mobile version providing access to schedules.");
        $this->addEntry("youthvb","Youthvolleyball.com","Youthvb code.");
        $this->addEntry("mwf","Mobile Web Framework","MWF code.");

        $this->newSection('Absolute references - (do not change based on dev platform):');
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

        // should compare entry to this->local, if they are the same, then we have a URL that matches.
        // possibly indicate that situation in the link somehow, not sure exactly how though
        $indicator = ( $this->local == $entry || $this->locals == $entry ) ?  " #" : "";

        $this->specified[] = $entry;
        $this->buf .= '<a href="' . $entry . '">' . $label . '</a> - ' . $desc ;
        $this->buf .= "$indicator<br />\n";
    }
    function output(){
        return $this->buf;
    }
}
$h = new htmlDoc("USYVL Utility Websites","");
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

$h->beg();

$uui = new usyvlUtilsIndex();
// this should be rejiggered using divs...
print <<<EOF
<table border=0 width='100%'>
<tr>
<td style='padding: 0px; width: 80px; '>
<img src="usyvllogo.jpg" align=left alt='USYVL Logo'><br>
</td>
<td class='banner'>
<h1>USYVL Utilities</h1>
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
