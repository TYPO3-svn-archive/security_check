<?php
/*
 * Created on 11.09.2006
 * @author axel.jung
 *
 */
require_once (dirname(__FILE__).'/conf.php');
require_once ('Find.php');
require_once (dirname(__FILE__).'/security_check_file_access.php');
class security_check_file_chmod extends security_check_file_access{
	/**
	 * @param string File Name
	 * @param string Validation Key
	 */
	function update($sFileName,$sValidationKey,$mode='files'){
		$newValue = ($mode==='files')? 0644:0755;
		if($this->checkAccess($sFileName,$sValidationKey)){
			if(file_exists($sFileName)){
				return @chmod ($sFileName,$newValue);

			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}
?>
