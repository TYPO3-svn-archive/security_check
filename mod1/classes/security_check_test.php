<?php
require_once (dirname(__FILE__).'/security_check_runner.php');
require_once (dirname(__FILE__).'/security_check_result.php');


define ('NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO',0);
define ('NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING',1);
define ('NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL',2);
/**
 * @version 1.0
 * @created 23-Aug-2006 10:59:39
 */
class security_check_test {
  var $_aResults;
  var $_current_test = '';
  var $sName = '';
  /**
   * Create a Result and ad it to the Runner
   * @param	boolean passed
   * @param	string message
   * @param	integer error_level
   * @return security_check_result
   */
  function _createResult($passed, $message='', $error_level=0) {
    $result = new security_check_result($passed,$message,$error_level);
    if(!isset($this->_aResults[$this->sName][$this->_current_test])){
    	$this->_aResults[$this->sName][$this->_current_test]['passed'] = true;
    }
    if(true !== $passed){
    	 $this->_aResults[$this->sName][$this->_current_test]['passed'] = false;
    }
	$this->_aResults[$this->sName][$this->_current_test]['results'][] = $result;
  }
  /**
   * @param mixed test
   * @param mixed compare
   * @param string message
   * @param integer level
   */
  function assertEquals($test, $compare, $message, $level) {
    if($test==$compare){
    	$this->_createResult(true);
    }else{
    	$this->_createResult(false,$message,$level);
    }
  }
  /**
   *
   * @param mixed test
   * @param string message
   * @param integer level
   */
  function assertFalse($test, $message, $level) {
  	if(false==$test){
    	$this->_createResult(true);
    }else{
    	$this->_createResult(false,$message,$level);
    }
  }
  /**
   *
   * @param mixed test
   * @param string message
   * @param integer level
   */
  function assertTrue($test, $message, $level) {
  	if(true==$test){
    	$this->_createResult(true);
    }else{
    	$this->_createResult(false,$message,$level);
    }
  }
  /**
   *
   * @param mixed test
   * @param string message
   * @param integer level
   */
  function assertNotEmpty($test, $message, $level) {
  	if(!empty($test)){
    	$this->_createResult(true);
    }else{
    	$this->_createResult(false,$message,$level);
    }
  }
  function skip($message){
	$this->_createResult(null,$message);
  }
  /**
   * Run all Test methods
   * @param array Results
   * @return array Results
   */
  function run($aResults) {
	$this->_aResults =$aResults;
	$arr = get_class_methods($this);
	foreach($arr as $func_name){
		if(ereg('test_',$func_name)){
			$this->_current_test = $func_name;
			$this->startUp();
			$this->$func_name();
			$this->tearDown();
		}
	}
	return $this->_aResults;
  }
  function startUp(){}
  function tearDown(){}
}
?>