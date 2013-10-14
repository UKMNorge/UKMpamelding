<?php
## CHECK IF WE GOT A TITLE TO DELETE
if(!empty($_POST['someID_input'])) {
	$delete = new SQL($delete,
				 array('t_id'=>$_POST['someID_input'],
					   'b_id'=>$_SESSION['B_ID'],
					   'season'=>$SEASON));
	$delete = $delete->run();
	$success = mysql_affected_rows()==1;
	
	## IF ACTUALLY DELETED, AND THERE IS 1 AFFECTED ROW, THERE IS A SUCCESS
	if($delete&&$success)	$MSG = array(true, $lang['title_deleted']);
	else $MSG = array(false, $lang['title_not_deleted']);

	## VALIDATE THE BAND
	require_once('include/validation.inc.php');
	validateBand($_SESSION['B_ID']);
}
?>