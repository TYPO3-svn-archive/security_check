<?php
require_once (dirname(__FILE__).'/conf.php');
require_once (dirname(__FILE__).'/security_check_result.php');
require_once (dirname(__FILE__).'/security_check_test.php');
require_once (dirname(__FILE__).'/security_check_renderer.php');
/**
 * @version 1.0
 * @author Axel Jung
 * @created 23-Aug-2006 10:59:38
 */
class security_check_runner {
	var $_aResults = array();
	var $_aTests = array();
	var $_oRenderer;
	/**
	 * Construcor
	 * @param security_check_renderer renderer
	 */
	function security_check_runner($renderer) {
		$this->_aRenderer = $renderer;
	}
	/**
	 * @param string Suitename
	 */
	function addSuite($suite) {
		require_once(dirname(__FILE__).'/'.$suite.'.php');
		$test = new $suite($this);
		if('security_check_test' !== get_parent_class($test)){
			exit('The Test must be a type of security_check_test');
		}
		array_push($this->_aTests,$test);
	}
	/**
	 * Run all Tests and store the reuslts
	 */
	function runTests() {
		if(is_array($this->_aTests)){
			foreach($this->_aTests as $test){
				$this->_aResults[$test->sName] = array();
				$this->_aResults = $test->run($this->_aResults);

			}
		}
	}
	function toHtml($oTemplate,$arr_lang) {
		return $this->_aRenderer->render($this->_aResults,$oTemplate,$arr_lang);
	}

}
?>