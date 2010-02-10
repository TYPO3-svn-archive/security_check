<?php
/*
 * Created on 11.09.2006
 * @author axel.jung
 *
 */
class security_check_file_access{
	var $_encryption_key = 'djsdf8o3380/()%6357&';
	/**
	 * Check the Access to the File
	 * @param	string	$sFileName			Filename
	 * @param	string	$sValidationKey		Key
	 * @return	boolean	[true] if Key is Valid
	 * @access	protected
	 */
	function checkAccess($sFileName,$sValidationKey){
		$sCompare = $this->getKey($sFileName);
		if($sCompare === $sValidationKey){
			return true;
		}else{
			return false;
		}

	}
	/**
	 * Get a Validation Key for Filename
	 * @param	string	$sFileName	Filename
	 * @return 	string	Validation Key
	 */
	function getKey($sFileName){
		$sValidationKey = '';
		$sValidationKey = $this->_encryption_key.$sFileName.$_SERVER['HTTP_HOST'];
		$sValidationKey = md5($sValidationKey);
		return $sValidationKey;
	}
}
?>
