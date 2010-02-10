<?php
/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:38
 */
class security_check_result{
	/**
	 * @var integer Error Level
	 */
	var $_iError_level = 0;
	/**
	 * @var string Message
	 */
	var $_sMessage = '';
	/**
	 * @var string Test method
	 */
	var $_sTestMethod = '';
	/**
	 * @var boolean If the Test passed
	 */
	var $_bPassed = false;
	/**
	 * @var boolean if the Test is Skipped
	 */
	var $_bSkiped = false;
	/**
	 * Constructor
	 * @param boolean If the Test is passed
	 * @param string Message
	 * @param int Error Level NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO, NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING, NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL
	 *
	 */
	function security_check_result($passed,$message,$error_level){
		if(is_null($passed)){
			$this->_bSkiped = true;
			$this->_bPassed = false;
		}else{
			$this->_bSkiped = false;
			$this->_bPassed = $passed;
		}
		$this->_sMessage = $message;
		$this->_iError_level = $error_level;
	}
	/**
	 * @return int Error Level
	 */
	function getErrorLevel(){
		return $this->_iError_level;
	}
	/**
	 * @return string Message
	 */
	function getMessage(){
		return $this->_sMessage;
	}
	/**
	 * @return boolean weather the Test is passed
	 */
	function isPassed(){
		return $this->_bPassed;
	}
	/**
	 * @return boolean If the test was skipped
	 */
	function isSkiped(){
		return $this->_bSkiped;
	}

}
?>