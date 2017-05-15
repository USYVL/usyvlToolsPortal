<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/local/lib/site-php');

require_once("htmlDoc.php");
$h = new htmlDoc("USYVL Utility Websites","");
$h->setHeading();

$h->addStyle("body { font-family: arial,verdana,helvetica; font-size: 10pt; }");
$h->addStyle("td { padding: 0px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 14; color: 000000; border: 0;}");
$h->addStyle("table { border-collapse: collapse;}");
$h->addStyle(".banner { padding-left: 10px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 12; color: 0000ff; border: 0;}");
$h->addStyle(".rcell { padding-left: 10px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 12; color: 0000ff; border: 0;}");
$h->addStyle("h1 { padding: 0px; margin: 0px; vertical-align: top; font-family: arial,verdana,helvetica; font-size: 20pt; font-style: bold; color: 000000; border: 0;}");

$h->beg();

print <<<EOF
<table border=0 width='100%'>
<tr>
<td style='padding: 0px; width: 80px; '>
<img src="usyvllogo.jpg" align=left alt='USYVL Logo'><br>
</td>
<td class='banner'>
<h1>USYVL Utilities</h1>
<p>
These following sites provide tools for maintaining.specific aspects of the 
USYVL website.
</p>
</td>
</tr>
</table>
<br>
EOF;

$h->a_br("scheduling","USYVL Schedule Building Utilities (Current revision)","Tools to maintain the Game Schedules portion of the website.");
$h->a_br("previous-sched","USYVL Schedule Building Utilities (Older revision)","Backup copy (not completely up-to-date) in case the most current revision has unforseen issues.");
$h->a_br("sandbox-sched","USYVL Schedule Building Utilities (Sandbox copy)","Sandbox version - file xfers have different destinations.");
$h->a_br("donate","USYVL Donorlist Builder","Tools to create and maintain the donorlist image on the Donate page.");
$h->a_br("workflow","USYVL workflow","Workflow system to manage sites over the course of a season.");
$h->a_br("mobile","USYVL mobile","Mobile version providing access to schedules.");
$h->a_br("http://www.usyvl.org","USYVL Home Page","USYVL Live Public Server");
$h->a_br("wiki","Wiki for USYVL software development/deployment","USYVL development wiki");
$h->end();
?>
