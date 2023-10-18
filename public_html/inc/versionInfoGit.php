<?php
class versionInfoGitClass {
	private  $gitBasePath;
	public   $gitBranchName;
	public   $gitHash;
	public   $gitDate;
	private  $gitDSVersion;
	function __construct($path = '../.git'){
        $gitBasePath = $path;   // e.g in laravel: base_path().'/.git';
        $gitStr = file_get_contents($gitBasePath.'/HEAD');
        $this->gitBranchName = rtrim(preg_replace("/(.*?\/){2}/", '', $gitStr));                                                                                            
        $gitPathBranch = $gitBasePath.'/refs/heads/'.$this->gitBranchName;
        $this->gitHash = file_get_contents($gitPathBranch);
        $this->gitDate = date('Y-m-d H:i:s', filemtime($gitPathBranch));
        $this->gitDSVersion = date('Y.m.d', filemtime($gitPathBranch));
	}
	function info(){
        return "branch: ".$this->gitBranchName."<br>last commit: ".$this->gitDate."<br>commit hash: ".$this->gitHash;                                                       
	}
	function version(){
		return "$this->gitDSVersion";
	}
	function branch(){
		return "$this->gitBranchName";
	}
}
$GLOBALS['gitVersionInfo'] = new versionInfoGitClass();
?>