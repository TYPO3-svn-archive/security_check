<?php
require_once (dirname(__FILE__).'/security_check_test.php');
require_once ('Find.php');
/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:39
 */
class security_check_test_file_rights extends security_check_test{
	var $sName= 'File rights';
	function test_folder_rights(){
		$arr =  $this->findFolders();
		$this->assertTrue(empty($arr),count($arr). ' Folders have to much File permissons (755 recommed)',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);

	}
	function test_file_rights(){
		$arr =  $this->findFiles();
		$this->assertTrue(empty($arr),count($arr). ' Files have to much File permissons (644 recommed)',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function getRights($file){
		$perms = sprintf("%o",fileperms($file));
		$perms = intval(substr($perms,2));
		return $perms;
	}
	function findFiles(){
		$arr =  File_Find :: search('*.*', PATH_site, 'shell', 'files');
		$arrBad = array();
		foreach($arr as $file){
			if($this->getRights($file)>644){
				array_push($arrBad,$file);
			}

		}
		return $arrBad;
	}
	function findFolders(){
		$arr =  File_Find :: search('*/*/', PATH_site, 'shell', 'dirs');
		$arrBad = array();
		foreach($arr as $folder){
			if($this->getRights($folder)>755){
				array_push($arrBad,$folder);
			}

		}
		return $arrBad;
	}

}
?>