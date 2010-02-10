<?php
require_once (dirname(__FILE__).'/conf.php');
require_once (dirname(__FILE__).'/security_check_test.php');
require_once ('Find.php');

/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:39
 */
class security_check_test_typo3_access extends security_check_test{

	var $sName = 'Backend Access';
	var $sBackendDir;
	var $sHttpRoot;
	function security_check_test_typo3_access(){
		$arr = File_Find::search('*alt_main.php',PATH_site,'shell');
		if(!empty($arr)){
			$this->sBackendDir = substr(dirname($arr[0]),strlen(PATH_site));
		}
		$this->sHttpRoot = substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],'/typo3conf/'));
	}
	function test_access_backend(){
		if(is_null($this->sBackendDir)){
			$this->skip('Typo3-Backend Path not Found');
			return;
		}
		$this->assertTrue(file_exists(PATH_site.$this->sBackendDir.'/.htaccess'),'There is no .htaccess in your Typo3-Backend Path',NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);

		$url = 'http://'.$_SERVER['HTTP_HOST'].$this->sHttpRoot.'/'.$this->sBackendDir.'/';
		$f = @fopen($url,'r');
		$this->assertFalse($f,'Your Backend Tool is accessable: '.$url,NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}
	function test_access_install_tool(){
		if(is_null($this->sBackendDir)){
			$this->skip('Typo3-Backend Path not Found');
			return;
		}
		$url = 'http://'.$_SERVER['HTTP_HOST'].$this->sHttpRoot.'/'.$this->sBackendDir.'/install';
		$f = @fopen($url,'r');
		$this->assertFalse($f,'Your Install Tool is accessable: '.$url,NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}
}
?>