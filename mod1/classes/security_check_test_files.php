<?php

require_once (dirname(__FILE__).'/conf.php');
require_once (dirname(__FILE__).'/security_check_test.php');
require_once ('Find.php');
/**
 * Search the File System for useless Files
 * it use the PEAR Class File_Find
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:38
 */
class security_check_test_files extends security_check_test {
	var $sName = 'Files Check';
	/**
	 * @var	string	Regular Expression to find Files without Extesnion
	 */
	var $sRegExFilesWitoutExtension = '/^[\w\d\/\-]+$/i';
	function test_backup_files() {
		$arr = $this->findBackUpFiles();
		$this->assertTrue(empty ($arr), count($arr) . ' Backup Files found', NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function test_cvs_files() {
		$arr = $this->findFilesCvs();
		$this->assertTrue(empty ($arr), count($arr) . ' CVS dirs found, this files shouldn\'t be on the live Server', NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
	}
	function test_files_without_ext() {
		$arr = $this->findFilesWithoutExtension(PATH_site);
		$this->assertTrue(empty ($arr), count($arr) . ' Files without File Extension found', NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
	}
	function test_inc_files() {
		$arr = $this->findFilesInc();
		$this->assertTrue(empty ($arr), count($arr) . ' .inc files found, this files could be viewed in th Browser', NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
	}
	function test_readme_files() {
		$arr = $this->findReadmeFiles();
		$this->assertTrue(empty ($arr), count($arr) . ' README.txt files found, this files shouldn\'t be on the live Server', NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
	}
	function test_svn_files() {
		$arr = File_Find :: search('*svn', PATH_site, 'shell', 'dirs');
		$this->assertTrue(empty ($arr), count($arr) . ' svn dirs found, this files shouldn\'t be on the live Server', NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
	}
	/**
	 * @param	string	Directory Path
	 * @return	array 	Filenames wich hav no exstension
	 */
	function findFilesWithoutExtension($sPath){
		$arr = array();
		list ($directories,$files)  = File_Find::maptree($sPath);
		foreach($files as $file){
			if(preg_match($this->sRegExFilesWitoutExtension,basename($file))){
				array_push($arr,$file);
			}
		}
		return $arr;
	}
	/**
	 * Find CSV Directorys
	 * @return	array	CVS Paths
	 */
	function findFilesCvs(){
		return File_Find :: search('*/CVS', PATH_site, 'shell', true,'dirs');
	}
	/**
	 * Find Files with the Extension .inc
	 * @return	array	File Names with the Extension .inc
	 */
	function findFilesInc(){
		$arr = File_Find :: search('*.inc', PATH_site, 'shell', 'files');
		$arr_new = array();
		foreach($arr as $key=>$item){
			if(!strstr($item,'/typo3/')){
				$arr_new[] = $item;
			}
		}
		return $arr_new;
	}
	/**
	 * Find Readme.txt Files
	 * @return	array	File Names with the name README.txt
	 */
	function findReadmeFiles(){
		return File_Find :: search('*README.txt', PATH_site, 'shell', 'files');
	}
	/**
	 * Find Backupfiles
	 * @return	array	File Names
	 */
	function findBackUpFiles(){
		$arr = array();
		array_merge($arr,File_Find :: search('*.old', PATH_site, 'shell', 'files'));
		array_merge($arr,File_Find :: search('*.bak', PATH_site, 'shell', 'files'));
		array_merge($arr,File_Find :: search('*.older', PATH_site, 'shell', 'files'));
		return $arr;
	}
}
?>