<?php
## DELETE RELATION
$rel_infos = array('b_id'=>$_SESSION['B_ID'], 'p_id'=>$_POST['someID_input'], 'season'=>$SEASON);
$del_rel = new SQL("DELETE FROM `smartukm_rel_b_p` 
				   WHERE `b_id` = '#b_id'
				   AND `p_id`='#p_id'
				   AND`season`='#season'", $rel_infos);
$del_rel = $del_rel->run();

## GIVE CORRECT FEEDBACK TO USER
if($del_rel) {
	$MSG = array(true, $lang['people_deleted']);
//	stat_realtime_remove($KOMMUNE_ID, $_SESSION['B_ID'], $_POST['someID_input'], $SEASON);
	require_once('UKM/innslag.class.php');
	$band_object_2013 = new innslag($_SESSION['B_ID']);
	$band_object_2013->statistikk_oppdater();

	logIt($_SESSION['B_ID'], 18, $_POST['someID_input']);
} else {
	$MSG = array(false, $lang['people_not_deleted']);	
}

## VALIDATE THE BAND ONCE AGAIN
require_once('include/validation.inc.php');
validateBand($_SESSION['B_ID']);
?>