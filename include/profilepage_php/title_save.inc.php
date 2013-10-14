<?php
## IF A NEW TITLE, PERFORM A INSERT
	if(isset($_POST['t_id']) && $_POST['t_id'] == 'new') {
		$title = new SQL($insert,$title_infos);
		$title = $title->run();
		$t_id = mysql_insert_id();
		$log = 19;
## IF ALREADY EXISTING TITLE, PERFORM AN UPDATE
	} else {
		$title_infos = array_merge($title_infos, array('t_id'=>$_POST['t_id']));
		$title = new SQL($update, $title_infos);
		$title = $title->run();
		$saveMSG = true;
		$t_id = $_POST['t_id'];
		$log = 20;
	}
## IF UPDATE / INSERT SUCCESS, GIVE GOOD FEEDBACK AND LOG
	if($title) {
		$MSG = array(true, ((isset($saveMSG)&&$saveMSG) ? $lang['title_saved'] : $lang['title_added']));
		logIt($_SESSION['B_ID'], $log, $t_id);
## RETURN ERROR - NO LOG
	} else {
		$MSG = array(false, ($saveMSG ? $lang['title_not_saved'] : $lang['title_not_added']));	
	}
	
## VALIDATE BAND
require_once('include/validation.inc.php');
validateBand($_SESSION['B_ID']);
?>