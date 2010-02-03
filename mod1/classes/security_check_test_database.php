<?php
require_once (dirname(__FILE__).'/security_check_test.php');
/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:37
 */
class security_check_test_database extends security_check_test{

	var $sName = 'Database Check';
	var $_typoDB = '';
	function startUp(){
		$res = $GLOBALS['TYPO3_DB']->sql_query('SELECT DATABASE() AS typo3_db');
		$arr = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this->_typoDB = $arr['typo3_db'];
	}
	function test_acces_mysql_users(){
		if(!isset($GLOBALS['TYPO3_DB'])){
			$this->skip('No Access to Database');
			return;
		}
		$this->assertFalse($GLOBALS['TYPO3_DB']->sql_select_db('mysql'),'test_acces_mysql_users_error',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);
	}
	function test_hosts_restrictions(){
		if(!isset($GLOBALS['TYPO3_DB'])){
			$this->skip('No Access to Database');
			return;
		}

		if(false === $GLOBALS['TYPO3_DB']->sql_select_db('mysql')){
			$this->skip('No Access to User Table');
			return;
		}
		$res = $GLOBALS['TYPO3_DB']->sql('mysql','SELECT user FROM mysql.user WHERE Host="%" OR Host LIKE ""');
		$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		$this->assertTrue($count==0,'test_hosts_restrictions_error',NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING);

	}
	function test_password(){
		if(!isset($GLOBALS['TYPO3_DB'])){
			$this->skip('No Access to Database');
			return;
		}
		if(false === $GLOBALS['TYPO3_DB']->sql_select_db('mysql')){
			$this->skip('No Access to User Table');
			return;
		}
		$res = $GLOBALS['TYPO3_DB']->sql('mysql','SELECT user FROM mysql.user WHERE Password=""');
		$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		$this->assertTrue($count==0,'test_password_error',NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL);
	}
	function tearDown(){
		$GLOBALS['TYPO3_DB']->sql_select_db($this->_typoDB);
	}
}
?>