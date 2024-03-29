<?php
class usyvlUtilsIndex {
    private   string  $server;
    private   string  $requri;
    private   string  $dir;
    private   string  $local;
    private   string  $localS;
    private   string  $buf;
    private   array   $skipped;
    private   array   $fileList;
    private   array   $others;

    function __construct(){
        $this->server = $_SERVER['SERVER_NAME'];
        $this->requri = $_SERVER['REQUEST_URI'];
        $this->dir = dirname($_SERVER['SCRIPT_NAME']);
        $this->local = 'http://' . $this->server . $this->dir;
        $this->localS = 'https://' . $this->server . $this->dir;
        $this->buf = '';

        // start this list off with files/dirs we do NOT want to show up at the bottom (optional)
        $this->skipped = array('.git','.gitignore','README.md','Index.php','usyvllogo.jpg','unused','testing','krumo-1.6','wiki-db','.DS_Store','config.php','config.php.default','version.php','css');
        $this->fileList = array();
        // alternatively, we may want to just scan for symlinks now

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
        $this->others = array_diff($this->fileList,$this->skipped);
        //print_pre($this->fileList,"filelist");
        //print_pre($others,"others");

        if ( count($this->others) == 0) return;
        $this->newSection('Dynamically Loaded, Unskipped Resources existing on this portal:');
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
    	// Potentially, this "data" should be managed in the index.php file, but will leave this for now
        $this->newSection('Local Resources specific to this installation - Scheduling - backups, sandboxes, etc... (copy is specific to this dev or hosting):');
        $this->addEntry("scheduling","USYVL Scheduling (Curr)","Tools to maintain the Game Schedules portion of the website.");
        $this->addEntry("sched-prev","USYVL Scheduling (Prev)","Previous (stable) version in case the most current revision has unforseen issues.");
        $this->addEntry("sched-dev" ,"USYVL Scheduling (Dev)","Development version for testing.");
        $this->addEntry("sandbox-sched","USYVL Schedule Building Utilities (Sandbox copy)","Sandbox version - file xfers have different destinations.");

        $this->addEntry("donate","USYVL Donorlist Builder","Tools to create and maintain the donorlist image on the Donate page.");
        $this->addEntry("workflow","USYVL workflow","Workflow system to manage sites over the course of a season.");
        $this->addEntry("mobile","USYVL mobile","Mobile version providing access to schedules.");
        $this->addEntry("youthvb","Youthvolleyball.com","Youthvb code.");
        $this->addEntry("wiki","USYVL Wiki","USYVL Development Documentation Wiki");
        $this->addEntry("mwf","Mobile Web Framework","MWF code.");

        $this->newSection('Remote/External Resources - referenced through a fully qualified URI (does\'t change based on development/hosting platform):');
        $this->addEntry("https://www.usyvl.org","production site5","USYVL Organizational Public Server");
        $this->addEntry("https://m.usyvl.org","production site5","USYVL Mobile Site");
        $this->addEntry("https://mwf.usyvl.org","production site5","USYVL Mobile Web Framework Site (Supports Mobile Site)");
        $this->addEntry("https://schedules.usyvl.org","production site5","USYVL Schedules Site");
        $this->addEntry("https://youthvolleyball.com","production site5","YouthVolleyball.com Feeder site");
        $this->addEntry("https://tools.usyvl.org/wiki","production linode","USYVL Development Documentation Wiki");
        $this->addEntry("https://s.usyvl.org/","production linode","schedules site tied in directly to scheduling system - served from linode.usyvl.org");
        $this->addEntry("https://mwf8.usyvl.org/","development linode","Mobile Web Framework served from linode.usyvl.org");
        $this->addEntry("https://tools.usyvl.org/","development linode","USYVL Tools Portal");
        $this->addEntry("http://localhost:8080/usyvl","development aaron home","USYVL Tools Portal (home dev)");
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
        $exists   = file_exists($entry);
        $absurl   = preg_match('/^https*:\/\//',$entry);
        $lversion  = '';

        if (! $exists && ! $absurl ) return;   // skip any non-existent entry

        // could compare entry to this->local, if they are the same, then we have a URL that matches.
        // indicating that this copy, is the absolute URL referenced.
        // possibly indicate that situation in the link somehow, not sure exactly
        // how or what we want to do and if there is a real significance in it.
        // only really applicable to the wiki it looks like.
        $indicator = '';
        $col2 = '';
        $col1 = '';
        $col3 = '';
        if ($absurl){
            if ( $this->local == dirname($entry) || $this->localS == dirname($entry) ){
                // the server is the same
                $indicator = " #";

                // since the entry skipped is on the local server see if the basename
                // portion matches a directory at this location, if so tag it so it doesn't
                // show up in the other section.
                $bn = basename($entry);
                if( file_exists($bn) && is_dir($bn)){
                    $this->skipped[] = $bn;
                }
                // $col1 = '<div class="row"><span><a href="' . $entry . '">' . $label . '</a></span>' ;
            }
            else {
                // not running on this server
                $col1 = "<span class=\"sub-version\"><a href=\"$entry\">$entry</a></span>";
                $col2 = "<span>$label</span>\n";
                $col3 = "<span>$desc</span>\n";
            }
        }
        else {   // This is for locally hosted repos, we can't get this info for remote repos
            $col1 = '<div class="row"><span><a href="' . $entry . '">' . $label . '</a></span>' ;
            $col3 = "<span>$desc</span>\n";


            // want/need to add a .git based version like the code is using, but the way I set things
            // up I can't see the .git folder of the entry.  Hmmm, maybe I can
            // echo "checking path: " . $entry . '/../.git' . "<br>\n";
            if (file_exists($entry . '/../.git') && is_dir($entry . '/../.git')){
                $einfo = new versionInfoGitClass($entry . '/../.git');
                $lversion = $einfo->version();
                $lbranch  = $einfo->branch();
                if (isset($lversion) && $lversion != ''){
                    $col2="<span class=\"sub-version\">(v{$lversion} - {$lbranch})</span>";
                }
            }
            // see if a version.php file exists for USYVL developed code
            elseif (file_exists($entry . '/version.php')){
                include $entry . '/version.php';
                $lversion = $GLOBALS['version'];
                if (isset($lversion) && $lversion != ''){
                    $col2="<span class=\"sub-version\">(v$lversion)</span>";
                }
            }
            // This case if for Mediawiki install underneath the portal
            if (file_exists($entry . '/includes/Defines.php')){
                $m = array();
                // this is going to be harder to pull out with php
                $definesContent = file($entry . '/includes/Defines.php');
                foreach($definesContent as $line){
                    if (preg_match('/MW_VERSION.*([0-9]\.[0-9]*\.[0-9]*)\'/',$line,$m)){
                        $wg_version = preg_replace('[, a-zA-Z\']','',$line);
                        if( $m[1] != '' ){
                            $col2="<span class=\"sub-version\">(v{$m[1]})</span>";
                        }
                    }
                }
            }
        }

        $this->skipped[] = $entry;
        $this->buf .= "<div class=\"row\">";
        // $this->buf .= ( isset($lversion) && $lversion != '') ? " <span class=\"sub-version\">(v$lversion)</span> " : "<span class=\"sub-version\"></span> " ;
        $this->buf .= ( isset($col1) && $col1 != '') ? "$col1" : "" ;
        $this->buf .= ( isset($col2) && $col2 != '') ? "$col2" : "" ;
        $this->buf .= "<span>$desc</span>" ;
        $this->buf .= "$indicator</div><br>\n";
    }
    function output(){
        return $this->buf;
    }
}
?>