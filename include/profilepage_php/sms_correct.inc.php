<?php
## CHECK IF BAND IS VALIDATED
$validated = new SQL("SELECT `b_status`
					 FROM `smartukm_band`
					 WHERE `b_id` = '#b_id'",
					 array('b_id'=>$_SESSION['B_ID']));
$validated = $validated->run('field','b_status');
## IF NOT ALREADY VALIDATED, VALIDATE AND SEND E-MAIL
if($validated == 0) {
	$new = isset($STATUS) ? 8 : 1;
	$validate = new SQL("UPDATE `smartukm_band`
						SET `b_status` = '#status'
						WHERE `b_id` = '#b_id'",
						array('b_id'=>$_SESSION['B_ID'], 'status'=>$new));
	$validate = $validate->run();
	logIt($_SESSION['B_ID'], 10);
	
	## SEND E-MAIL
	require_once('include/profilepage_php/email.inc.php');
}
?>