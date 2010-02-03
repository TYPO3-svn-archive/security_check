<?php
/*
 * Created on 11.09.2006
 * @author axel.jung
 *
 */
require_once (dirname(__FILE__).'/conf.php');
require_once ('Find.php');
require_once (dirname(__FILE__).'/security_check_file_access.php');
class security_check_file_delete extends security_check_file_access{
	/**
	 * @param string File Name
	 * @param string Validation Key
	 */
	function delete($sFileName,$sValidationKey){
		if($this->checkAccess($sFileName,$sValidationKey)){
			if(file_exists($sFileName)){
				return @unlink($sFileName);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function deleteAll($aFileNames,$sValidationKey){

	}
}
?>
