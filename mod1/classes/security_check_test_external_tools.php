<?php
require_once (dirname(__FILE__).'/security_check_test.php');
require_once ('Find.php');
require_once ('HTTP/Request.php');
/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:38
 */
class security_check_test_external_tools extends security_check_test
{
	var $sName = 'External Tools';
	function test_phpmyadmin(){
		$arr = File_Find :: search('*phpMyAdmin*index.php', PATH_site, 'shell');
		$arr_php_myadmin = array();
		foreach($arr as $key => $files){
			if(file_exists(dirname($files).'/config.inc.php')){
				$arr_php_myadmin[]=$files;
			}
		}

		//$this->assertTrue(empty ($arr_php_myadmin), count($arr_php_myadmin). ' phpmyadmin dirs found ', NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
		$req =& new HTTP_Request("");
		foreach($arr_php_myadmin as $file){
			$sHttpRoot = substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],'/typo3conf/'));
			$link = substr($file,strlen(PATH_site));
			$protocol = ($_SERVER['SERVER_PORT']==443)?'https://':'http://';
			$url = $protocol.$_SERVER['HTTP_HOST'].$sHttpRoot.'/'.$link;
			$req->setURL($url);
		    $req->sendRequest();
		    $code = $req->getResponseCode();
		    $content = $req->getResponseBody();
		    $this->assertFalse(strstr($content,'phpMyAdmin'),$link.' is accessable',NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		}
	}
	function test_phpinfo(){
		$arr = File_Find :: search('*info.php', PATH_site, 'shell', 'dirs');
		//$this->assertTrue(empty ($arr), count($arr). ' info.php files found ', NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
		$req =& new HTTP_Request("");
		foreach($arr as $file){
			$sHttpRoot = substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],'/typo3conf/'));
			$link = substr($file,strlen(PATH_site));
			$url = 'http://'.$_SERVER['HTTP_HOST'].$sHttpRoot.'/'.$link;
			$req->setURL($url);
		    $req->sendRequest();
		    $code = $req->getResponseCode();
		    $content = $req->getResponseBody();
			$this->assertFalse(strstr($content,'phpinfo'),$link.' is accessable and shows phpinfo',NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		}
	}

}
?>