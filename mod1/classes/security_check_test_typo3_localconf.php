<?php
require_once (dirname(__FILE__).'/security_check_test.php');

/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:39
 */
class security_check_test_typo3_localconf extends security_check_test{

	var $_TYPO3_CONF_VARS;
	var $sName = 'Localconf Settings';
	function security_check_test_typo3_localconf(){
		global $TYPO3_CONF_VARS;
		$this->_TYPO3_CONF_VARS = $TYPO3_CONF_VARS;
	}
	function test_encryptionKey(){
		$this->assertTrue(isset($this->_TYPO3_CONF_VARS['SYS']['encryptionKey']),'You should set the encryptionKey',NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}

	function test_fileCreateMask(){
		$this->assertEquals($this->_TYPO3_CONF_VARS['BE']['fileCreateMask'],'0644','fileCreateMask should be 0644',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}

	function test_folderCreateMask(){
		$this->assertEquals($this->_TYPO3_CONF_VARS['BE']['folderCreateMask'],'0755','folderCreateMask should be 0755',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}

	function test_install_tool_password(){
		$this->assertFalse($this->_TYPO3_CONF_VARS['BE']['installToolPassword'] == 'bacb98acf97e0b6112b1d1b650b84971','Your Install Tool password is joh316, please chaneg it',NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}

	function test_lockSSL(){
		$this->assertTrue($this->_TYPO3_CONF_VARS['BE']['lockSSL'],'lockSSL is not activated',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);

	}

	function test_loginSecurityLevel_superchallenged(){
		if(empty($this->_TYPO3_CONF_VARS['BE']['loginSecurityLevel'])){
			$this->assertEquals($this->_TYPO3_CONF_VARS['BE']['loginSecurityLevel'],'','loginSecurityLevel should be superchallenged',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
		}else{
			$this->assertEquals($this->_TYPO3_CONF_VARS['BE']['loginSecurityLevel'],'superchallenged','loginSecurityLevel should be superchallenged',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
		}
	}
	function test_warning_email_addr(){
		$this->assertNotEmpty($this->_TYPO3_CONF_VARS['BE']['warning_email_addr'],'warning_email_addr should be set',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function test_sessionTimeout_to_high(){
		$this->assertTrue($this->_TYPO3_CONF_VARS['BE']['sessionTimeout']<=3600,'The sessionTimeout is to heigh',NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
	}
	function test_sql_debug_off(){
		$this->assertFalse($this->_TYPO3_CONF_VARS['SYS']['sqlDebug'],'sqlDebug should be disabled',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function test_displayErrors(){
		$this->assertFalse($this->_TYPO3_CONF_VARS['BE']['displayErrors'],'displayErrors should be disabled',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function test_allowGlobalInstall(){
		$this->assertFalse($this->_TYPO3_CONF_VARS['EXT']['allowGlobalInstall'],'allowGlobalInstall should be disabled',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function test_disable_exec_function(){
		$this->assertTrue($this->_TYPO3_CONF_VARS['BE']['disable_exec_function'],array('error'=>'test_disable_exec_function_error','tipp'=>'test_disable_exec_function_tipp'),NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function test_noEdit(){
		$this->assertTrue($this->_TYPO3_CONF_VARS['EXT']['noEdit'],'noEdit should be enabled',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}


}
?>