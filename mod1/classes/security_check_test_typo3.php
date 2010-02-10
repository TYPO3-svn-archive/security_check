<?php
require_once (dirname(__FILE__).'/security_check_test.php');

/**
 * @author Axel Jung
 * @version 1.1
 * @created 23-Aug-2006 10:59:39
 */
class security_check_test_typo3 extends security_check_test {

	var $sName = 'Typo3 Check';
	function test_standard_password() {
		$sql = 'SELECT uid FROM be_users WHERE password="5f4dcc3b5aa765d61d8327deb882cf99" AND deleted=0';
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

		$this->assertTrue($count == 0, $count . ' User mit den Password:password gefunden', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}
	function test_extension_black_list(){
		$this->assertFalse($this->_check('tipafriend','1.2.1'),'test_extension_black_list_err_tipafriend', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$this->assertFalse($this->_check('dam_downloads','1.0.1'),'test_extension_black_list_err_dam_downloads', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$this->assertFalse($this->_check('chc_forum','1.4.4'),'test_extension_black_list_err_chc_forum', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$this->assertFalse($this->_check('th_mailformplus','3.6.1'),'test_extension_black_list_err_th_mailformplus', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$this->assertFalse($this->_check('fe_rtenews','0.4.3'),'test_extension_black_list_err_fe_rtenews', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$this->assertFalse($this->_check('moc_filemanager','0.7.1'),'test_extension_black_list_err_moc_filemanager', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$this->assertFalse($this->_check('cc_awstats','0.9.0'),'test_extension_black_list_err_cc_awstats', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$this->assertFalse($this->_check('cmw_linklist','1.4.1'),'test_extension_black_list_err_cc_awstats', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		$t3version = t3lib_div::int_from_ver(TYPO3_version);
		if($t3version>3007000){
			$this->assertFalse($this->_check('rtehtmlarea','1.1.4'),'test_extension_black_list_err_rtehtmlarea', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		}
		if($t3version>3008000){
			$this->assertFalse($this->_check('rtehtmlarea','1.2.1'),'test_extension_black_list_err_rtehtmlarea', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		}
		if($t3version>4000000){
			$this->assertFalse($this->_check('rtehtmlarea','1.4.2',true,'L'),'test_extension_black_list_err_rtehtmlarea', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		}
		if($t3version>4000000){
			$this->assertFalse($this->_check('rtehtmlarea','1.3.8',true,'S'),'test_extension_black_list_err_rtehtmlarea', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		}
		if($t3version>4001000){
			$this->assertFalse($this->_check('rtehtmlarea','1.5.1'),'test_extension_black_list_err_rtehtmlarea', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
		}
	}
	function test_typo3_version(){

		$this->assertTrue($this->_compareVersion('4.0.4',TYPO3_version)>=0,'test_typo3_version_err', NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}
	/**
	 * Check the Version Requirement of a Plugin
	 * @param	string	Extension Key
	 * @param	string	Version Key
	 * @param	boolean If the Funktion search a Extension with lower Version
	 * @return	true if the Extension is found
	 */
	function _check($_EXTKEY,$version,$bLowerThan = true,$type=null){
		global $EM_CONF,$TYPO3_LOADED_EXT;
		if(!t3lib_extMgm::isLoaded($_EXTKEY)){
			return false;
		}
		if(null !=$type){
			if($TYPO3_LOADED_EXT[$_EXTKEY]['type']!=$type){
				return false;
			}
		}
		require_once t3lib_extMgm::extPath($_EXTKEY).'ext_emconf.php';
		$sCurrentVersion = $EM_CONF[$_EXTKEY]['version'];
		$iVersion = t3lib_div::int_from_ver($sCurrentVersion);
		$iVersionCompare = t3lib_div::int_from_ver($version);
		if(true === $bLowerThan){
			if($iVersion >=$iVersionCompare){
				return false;
			}
			else{
				return true;
			}
		}else{
			if($iVersion ==$iVersionCompare){
				return true;
			}
			else{
				return false;
			}
		}

	}
	/**
	 * Compare Version Strings
	 * @param	string	Version
	 * @param	string	Version to Compare
	 * @return	int	0 if equal, -1 if sVersion lower than sVersionCompare, 1 if sVersion higher than sVersionCompare
	 */
	function _compareVersion($sVersion,$sVersionCompare){

		$iVersion = t3lib_div::int_from_ver($sVersion);
		$iVersionCompare = t3lib_div::int_from_ver($sVersionCompare);
		if($iVersion == $iVersionCompare){
			return 0;
		}else if($iVersion < $iVersionCompare){
			return -1;
		}else{
			return 1;
		}
	}

}
?>