<?php


/**
 * @author Axel Jung
 * @version 1.0
 * @created 23-Aug-2006 10:59:38
 */
class security_check_renderer{
	/**
	 * @var array security_check_result
	 */
	var $_aResults = array();
	var $_oTemplate ;
	var $_arr_lang = array();
	function getLL($key){
		if(isset($this->_arr_lang[$key])){
			return $this->_arr_lang[$key];
		}else{
			return $key;
		}
	}
	/**
	 * Render the Results
	 * @param array [suitename][methodname][][security_check_result]
	 * @return string Html
	 */
	function render($results,$template,$arr_lang){
		$this->_arr_lang = $arr_lang;
		$this->_oTemplate = $template;
		$content .= '<style>';
		$content .= '.testmethod{font-weight:bold}';
		$content .= '.passed{background-color:green}';
		$content .= '.failed{background-color:gray}';
		$content .= '.skipped{background-color:silver}';
		$content .= '.error_info{background-color:yellow}';
		$content .= '.error_warning{background-color:orange}';
		$content .= '.error_fatal{background-color:red}';
		$content .= '.security-check-fieldset{border:1px solid gray;}';
		$content .= '</style>';

		$this->_aResults = $results;
		if(true === is_array($this->_aResults)){
			foreach($this->_aResults as $key=> $aSuite){
				$content .= $this->_renderSuite($key,$aSuite);
			}
		}
		//$content .= '</table>';
		return $content;
	}
	/**
	 * @param string Suite Name
	 * @param array Suite Methods
	 * @return string html
	 */
	function _renderSuite($sSuiteName,$aSuiteMethods){
		$sCssID= str_replace(' ','-',$sSuiteName);
		$sIconMinus = t3lib_iconWorks::skinImg($this->_oTemplate->backPath,'gfx/ol/minusbullet.gif','',1);
		$sIconPlus = t3lib_iconWorks::skinImg($this->_oTemplate->backPath,'gfx/ol/plusbullet.gif','',1);
		$content = '<fieldset class="security-check-fieldset"><legend class="testmethod">'.$sSuiteName.' ';
		$content .= '<img src="'.$sIconMinus.'" ';
		$content .= ' onclick="if(document.getElementById(\''.$sCssID.'\').style.display!=\'none\'){this.src=\''.$sIconPlus.'\';document.getElementById(\''.$sCssID.'\').style.display=\'none\';}else{this.src=\''.$sIconMinus.'\';document.getElementById(\''.$sCssID.'\').style.display=\'\';}" />';
		$content .= '</legend>';
		$content .= '<table border="0" width="100%" cellpadding="5" id="'.$sCssID.'">';
		if(true === is_array($aSuiteMethods)){
			foreach($aSuiteMethods as $sMethodName => $aMethodsResults){
				$content .= $this->_renderSuiteMethods($sMethodName,$aMethodsResults);
			}
		}
		$content .= '</table></fieldset>';
		return $content;
	}
	/**
	 * @param string Method Name
	 * @param array Method Results
	 * @return string html
	 */
	function _renderSuiteMethods($sMethodName,$aMethodsResults){
		$content = '';
		if(true === $aMethodsResults['passed']){
			$sCss .= 'passed';
		}else{
			$sCss .= 'failed';
		}
		$content .= '<tr class="'.$sCss.'">';
		$content .= '<td colspan="2">';
		$content .= htmlentities($this->getLL($sMethodName));
		$content .= '</td>';
		if(true === $aMethodsResults['passed']){
			$content .= '<td class="'.$sCss.'" style="width:30px;">'.$sIcon = $this->_oTemplate->icons(-1).'</td>';
		}else{
			$content .= '<td class="'.$sCss.'" style="width:30px;">&nbsp;</td>';
		}
		$content .= '</tr>';
		if(false === $aMethodsResults['passed'] && true === is_array($aMethodsResults['results'])){
			foreach($aMethodsResults['results'] as  $oMethodsResult){
				if(false === $oMethodsResult->isPassed()){
					$content .= $this->_renderSuiteMethodsResults($oMethodsResult);
				}
			}
		}
		return $content;
	}
	/**
	 * @param object security_check_result
	 * @return string html
	 */
	function _renderSuiteMethodsResults($oMethodsResult){
		$content = '';
		if(true === $oMethodsResult->isPassed()){
			$sCss = 'passed';
			$sIcon = $this->_oTemplate->icons(-1);

		}elseif(true === $oMethodsResult->isSkiped()){
			$sCss = 'skipped';
			$sIcon = '';
		}else{
			switch($oMethodsResult->getErrorLevel()){
				case NIDAG_SECURITY_CHECK_ERROR_LEVEL_INFO:
					$sIcon = $this->_oTemplate->icons(1);
					$sCss = 'error_info';
					break;
				case NIDAG_SECURITY_CHECK_ERROR_LEVEL_WARNING:
					$sIcon = $this->_oTemplate->icons(2);
					$sCss = 'error_warning';
					break;
				case NIDAG_SECURITY_CHECK_ERROR_LEVEL_FATAL:
					$sIcon = $this->_oTemplate->icons(3);
					$sCss = 'error_fatal';
					break;
				default:
					$sIcon = $this->_oTemplate->icons(3);
					$sCss = 'error_fatal';
					break;
			}
		}

		$content .= '<tr>';
		$content .= '<td class="'.$sCss.'" colspan="2">';
		$err = $oMethodsResult->getMessage();
		if(is_array($err)){
			$content .= htmlentities($this->getLL($err['error']));
			if(isset($err['tipp'])){
				$content .= '<br/>'.$this->_oTemplate->icons(1).htmlentities($this->getLL($err['tipp']));
			}
		}else{
			$content .= htmlentities($this->getLL($err));
		}
		$content .= '</td>';
		$content .= '<td class="'.$sCss.'">';
		$content .= $sIcon;
		$content .= '</td>';
		$content .= '</tr>';
		return $content;
	}



}
?>