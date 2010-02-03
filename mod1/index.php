<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Axel Jung <info@jung-newmedia.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'Security Check' for the 'security_check' extension.
 *
 * @author	Axel Jung <info@jung-newmedia.de>
 */
unset($MCONF);

require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:security_check/mod1/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");


define('SECURITY_CHECK_CLASS_ROOT',t3lib_extMgm::extPath('security_check').'/mod1/classes/');
$BE_USER->modAccess($MCONF,1);
class tx_securitycheck_module1 extends t3lib_SCbase {
	var $pageinfo;
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();
	}
	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'runtests' => $LANG->getLL('function_runtests'),
				'list_unseless_files' => $LANG->getLL('function_list_unseless_files'),
				'list_to_much_file_rights' => $LANG->getLL('function_list_to_much_file_rights'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{
			$this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br />".$LANG->sL("LLL:EXT:lang/locallang_core.xml:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);
			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header(htmlentities($LANG->getLL("title")));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);
			$this->moduleContent();
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}
	/**
	 * Prints out the module HTML
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}
	/**
	 * Generates the module content
	 * @return	void
	 */
	function moduleContent()	{
		switch((string)$this->MOD_SETTINGS["function"])	{
			case 'runtests':
				$this->_runtests();
			break;
			case 'list_unseless_files':
				$this->_deleteFiles();
			break;
			case 'list_to_much_file_rights':
				$this->_fileRights();
			break;
		}
	}
	/**
	 * Function: Run tests
	 * Show the Start Button and the Reuslts
	 * @return	void
	 */
	function _runtests(){
		global $LANG,$LOCAL_LANG;
		$CMD =  t3lib_div::_GP('CMD');
		switch ($CMD) {
			case 'start_runtests':
				require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_renderer.php');
				require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_runner.php');
				$renderer = t3lib_div::makeInstance('security_check_renderer');
				$runner = new security_check_runner($renderer);
				$runner->addSuite('security_check_test_php_ini');
				$runner->addSuite('security_check_test_database');
				$runner->addSuite('security_check_test_typo3_localconf');
				$runner->addSuite('security_check_test_typo3_access');
				$runner->addSuite('security_check_test_files');
				$runner->addSuite('security_check_test_typo3');
				$runner->addSuite('security_check_test_external_tools');
				$runner->addSuite('security_check_test_file_rights');
				$runner->runTests();
				$content = $runner->toHtml($this->doc,$LOCAL_LANG[$LANG->lang]);
				$this->content.=$this->doc->section($LANG->getLL('show_runtests'),$content,0,1);
			default:
				$content = $LANG->getLL('intro_runtests');
				$content .= $this->doc->spacer(5);
				$content .= '<input name="CMD" type="hidden" value="start_runtests" />';
				$content .= '<input name="submit" type="submit" value="'. $LANG->getLL('title_runtests').'" onclick="this.disabled=1;this.value=\'........ '.utf8_decode($LANG->getLL('wait')).' .........\'" />';
				$this->content.=$this->doc->section($LANG->getLL('title_runtests'),$content,0,1);
				break;
		}
	}
	function _fileRights(){
		global $LANG;
		require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_test_file_rights.php');
		require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_file_chmod.php');
		$CMD =  t3lib_div::_GP('CMD');
		switch ($CMD) {
			case 'update_all':
				$sMode = t3lib_div::_GP('mode');
				$oFinder = t3lib_div::makeInstance('security_check_test_file_rights');
				$oDeleteService = t3lib_div::makeInstance('security_check_file_chmod');
				if($sMode=='files'){
					$aFiles = $oFinder->findFiles();
				}else{
					$aFiles = $oFinder->findFolders();
				}
				foreach($aFiles as $item){
					$content.= $item.'<br />';
					$content.= '<input name="item[]" type="hidden" value="'.$item.'" />';
					$content.= '<input name="validationcode[]" type="hidden" value="'.$oDeleteService->getKey($item).'" />';
				}
				$content.= '<input name="mode" type="hidden" value="'.$sMode.'" />';
				$content.= '<input name="CMD" type="hidden" value="update_all_confirm" />';
				$content.= '<input name="submit" type="submit" value="'.$LANG->getLL('update_all_confirm_submit').'" />';
				$this->content.= $this->doc->section($LANG->getLL('update_all'),$content,0,1);
				break;
			case 'update_all_confirm':
				$arr = t3lib_div::_GP('item');
				$arrValidationsCodes = t3lib_div::_GP('validationcode');
				$oDeleteService = t3lib_div::makeInstance('security_check_file_chmod');
				foreach($arr as $key=>$sFile){
					if(true ===$oDeleteService->update($sFile,$arrValidationsCodes[$key],t3lib_div::_GP('mode'))){
						$this->content .= '<p style="color:green">SUCCESS: '.$sFile.' updated.<p>';
					}else{
						$this->content .= '<p style="color:red">FAILURE: '.$sFile.' not updated.<p>';
					}
				}
			default:
				$this->_renderToMuchFileRights();
		}

	}
	/**
	 * Funtion: Unsecure and useless Files
	 * Show the Files and Delete them
	 * @return	void
	 */
	function _deleteFiles(){
		global $LANG;
		$CMD =  t3lib_div::_GP('CMD');
		switch ($CMD) {
			case 'delete':
				$content = $this->doc->spacer(5);
				$content.= t3lib_div::_GP('file');
				$content.= $this->doc->spacer(5);
				$content.= '<input name="file" type="hidden" value="'.t3lib_div::_GP('file').'" />';
				$content.= '<input name="validationcode" type="hidden" value="'.t3lib_div::_GP('validationcode').'" />';
				$content.= '<input name="CMD" type="hidden" value="delete_confirm" />';
				$content.= '<input name="submit" type="submit" value="'.$LANG->getLL('confirm_delete_submit').'" />';
				$this->content.= $this->doc->section($LANG->getLL('confirm_delete'),$content,0,1);
				break;
			case 'deleteall':
				$content = $this->doc->spacer(5);
				require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_test_files.php');
				$oFinder = t3lib_div::makeInstance('security_check_test_files');
				require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_file_delete.php');
				$oDeleteService = t3lib_div::makeInstance('security_check_file_delete');
				$sMode = t3lib_div::_GP('mode');
				$arrFiles = array();
				switch ($sMode) {
					case 'cvs':
						$arrFiles = $oFinder->findFilesCvs();
						break;
					case 'no_ext':
						$arrCVS = $oFinder->findFilesCvs();
						$arrNoExt = $oFinder->findFilesWithoutExtension(PATH_site);
						foreach($arrNoExt as $file){
							if(!in_array($file,$arrCVS)){
								array_push($arrFiles,$file);
							}
						}
						break;
					case 'inc':
						$arrFiles = $oFinder->findFilesInc(PATH_site);
						break;
					case 'readme':
						$arrFiles = $oFinder->findReadmeFiles(PATH_site);
						break;
					case 'backup':
						$arrFiles = $oFinder->findBackUpFiles(PATH_site);
						break;
				}
				foreach($arrFiles as $sFile){
					$content.= $sFile.' <br />';
					$content.= '<input name="file[]" type="hidden" value="'.$sFile.'" />';
					$content.= '<input name="validationcode[]" type="hidden" value="'.$oDeleteService->getKey($sFile).'" />';
				}
				$content.= $this->doc->spacer(5);

				$content.= '<input name="CMD" type="hidden" value="delete_confirm" />';
				$content.= '<input name="submit" type="submit" value="'.$LANG->getLL('confirm_delete_submit').'" />';
				$this->content.= $this->doc->section($LANG->getLL('confirm_delete'),$content,0,1);
				break;
			case 'delete_confirm':
				require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_file_delete.php');
				$oDeleteService = t3lib_div::makeInstance('security_check_file_delete');
				$file = t3lib_div::_GP('file');
				if(!is_array($file)){
					if(true ===$oDeleteService->delete($file,t3lib_div::_GP('validationcode'))){
						$this->content .= '<p style="color:green">SUCCESS: '.t3lib_div::_GP('file').' deleted.<p>';
					}else{
						$this->content .= '<p style="color:red">FAILURE: '.t3lib_div::_GP('file').' not deleted.<p>';
					}
				}else{
					$arrValidationsCodes = t3lib_div::_GP('validationcode');
					foreach($file as $key=>$sFile){
						if(true ===$oDeleteService->delete($sFile,$arrValidationsCodes[$key])){
							$this->content .= '<p style="color:green">SUCCESS: '.$sFile.' deleted.<p>';
						}else{
							$this->content .= '<p style="color:red">FAILURE: '.$sFile.' not deleted.<p>';
						}
					}
				}
			default:
				$this->_renderUselessFiles(PATH_site);
				break;
		}
	}
	/**
	 * Render a Delete File Link
	 * @param	string	Filename
	 * @param	string	Validation Key
	 * @param	string	Root Path of the Site
	 * @return	string	HTML Code
	 */
	function _renderDeletLink($sFileName,$key,$root){
		global $LANG;
		$sLink = '<a href="index.php?SET[function]='.$this->MOD_SETTINGS["function"].'&amp;CMD=delete&amp;file='.urlencode($sFileName).'&amp;validationcode='.$key.'" style="text-decoration:underline">'.$LANG->getLL('delete').'</a> '.substr($sFileName,strlen($root)).'<br />';
		return $sLink;
	}
	/**
	 * Render a Delete All Link
	 * @param	string	mode ('cvs','inc',..)
	 * @return	string	HTML Code
	 */
	function _renderDeletAllLink($sMode){
		global $LANG;
		$sLink = ' <a href="index.php?SET[function]='.$this->MOD_SETTINGS["function"].'&amp;CMD=deleteall&amp;mode='.urlencode($sMode).'" style="text-decoration:underline">'.$LANG->getLL('delete_all').'</a>';
		return $sLink;
	}
	function _renderUselessFiles($root){
		global $LANG;
		$this->content.=$this->doc->section($LANG->getLL('headline_useless_files'),$LANG->getLL('intro_useless_files'),0,1);
		require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_test_files.php');
		$oFinder = t3lib_div::makeInstance('security_check_test_files');
		require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_file_delete.php');
		$oDeleteService = t3lib_div::makeInstance('security_check_file_delete');
		// CVS
		$content = '';
		$arrCVS = $oFinder->findFilesCvs();
		foreach($arrCVS as $file){
			$content .= $this->_renderDeletLink($file,$oDeleteService->getKey($file),$root);
		}
		$this->content.=$this->doc->section($LANG->getLL('headline_useless_files_cvs').$this->_renderDeletAllLink('cvs'),$content,0,1,0,1);
		// No Extension
		$content = '';
		$arr = $oFinder->findFilesWithoutExtension(PATH_site);
		foreach($arr as $file){
			if(!in_array($file,$arrCVS)){
				$content .= $this->_renderDeletLink($file,$oDeleteService->getKey($file),$root);
			}
		}
		$this->content.=$this->doc->section($LANG->getLL('headline_useless_files_without_extension').$this->_renderDeletAllLink('no_ext'),$content,0,1,0,1);
		// inc Files
		$content = '';
		$arr = $oFinder->findFilesInc();
		foreach($arr as $file){
			$content .= $this->_renderDeletLink($file,$oDeleteService->getKey($file),$root);
		}
		$this->content.=$this->doc->section($LANG->getLL('headline_useless_files_inc').$this->_renderDeletAllLink('inc'),$content,0,1,0,1);
		// Readme Files
		$content = '';
		$arr = $oFinder->findReadmeFiles();
		foreach($arr as $file){
			$content .= $this->_renderDeletLink($file,$oDeleteService->getKey($file),$root);
		}
		$this->content.=$this->doc->section($LANG->getLL('headline_useless_files_readme').$this->_renderDeletAllLink('readme'),$content,0,1,0,1);
		// Backup Files
		$content = '';
		$arr = $oFinder->findBackUpFiles();
		foreach($arr as $file){
			$content .= $this->_renderDeletLink($file,$oDeleteService->getKey($file),$root);
		}
		$this->content.=$this->doc->section($LANG->getLL('headline_useless_files_backup').$this->_renderDeletAllLink('backup'),$content,0,1,0,1);
	}
	function _renderToMuchFileRights(){
		global $LANG;
		require_once(SECURITY_CHECK_CLASS_ROOT.'security_check_test_file_rights.php');
		$oFinder = t3lib_div::makeInstance('security_check_test_file_rights');
		$content = '';
		$arr = $oFinder->findFiles();
		foreach($arr as $file){
			$content .= $oFinder->getRights($file).' '.substr($file,strlen(PATH_site)).'<br />';
		}
		$sLink = ' <a href="index.php?SET[function]='.$this->MOD_SETTINGS["function"].'&amp;CMD=update_all&amp;mode=files" style="text-decoration:underline">'.$LANG->getLL('update_all').'</a>';
		$this->content.=$this->doc->section($LANG->getLL('headline_list_to_much_file_rights').$sLink,$content,0,1,0,1);

		$content = '';
		$arr = $oFinder->findFolders();
		foreach($arr as $folder){
			$content .= $oFinder->getRights($folder).' '.substr($folder,strlen(PATH_site)).'<br />';
		}
		$sLink = ' <a href="index.php?SET[function]='.$this->MOD_SETTINGS["function"].'&amp;CMD=update_all&amp;mode=folder" style="text-decoration:underline">'.$LANG->getLL('update_all').'</a>';
		$this->content.=$this->doc->section($LANG->getLL('headline_list_to_much_folder_rights').$sLink,$content,0,1,0,1);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/security_check/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/security_check/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_securitycheck_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>