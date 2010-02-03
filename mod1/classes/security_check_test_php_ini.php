<?php
require_once (dirname(__FILE__).'/security_check_test.php');



/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:38
 */
class security_check_test_php_ini extends security_check_test {
	var $sName = 'Php ini Check';
	function test_basedir_set() {
		$bOk = ini_get('open_basedir')!='';
		$this->assertTrue($bOk,'test_basedir_set_error',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}

	function test_error_log_on() {
		$bOk = ini_get('error_log')!=0;
		$this->assertTrue($bOk,array('error'=>'test_error_log_on_error','tipp'=>'test_error_log_on_tipp'),NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO);
	}

	function test_register_globals_off() {
		$bOk = ini_get('register_globals')==0;
		$this->assertTrue($bOk,array('error'=>'test_register_globals_off_error','tipp'=>'test_register_globals_off_tipp'),NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}

	function test_error_display_off() {
		$bOk = ini_get('display_errors')==0;
		$this->assertTrue($bOk,array('error'=>'test_error_display_off_error','tipp'=>'test_error_display_off_tipp'),NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}
	/*
	function test_enable_dl_off() {
		$bOk = ini_get('enable_dl')==0;
		$this->assertTrue($bOk,'The ini setting "enable_dl" should be off',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	*/

	function test_magic_quotes_off() {
		$this->assertFalse(get_magic_quotes_gpc(),array('error'=>'test_magic_quotes_off_error','tipp'=>'test_magic_quotes_off_tipp'),NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}

}
?>