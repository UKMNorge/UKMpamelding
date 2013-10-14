<?php
	## FIX RELATION (DELETE AND INSERT)
	$rel_infos = array('b_id'=>$_SESSION['B_ID'],
							 'p_id'=>$_SESSION['UKM_DINSIDE_UID'],
							 'season'=>$SEASON,
							 'p_function'=>$_POST['p_my_function']);
	$del_rel = new SQL("DELETE FROM `smartukm_rel_b_p` 
					   WHERE `b_id` = '#b_id'
					   AND `p_id`='#p_id'
					   AND`season`='#season'", $rel_infos);
	$del_rel = $del_rel->run();
	
	$b_p = "INSERT INTO `smartukm_rel_b_p`
		(`b_id` ,`p_id` ,`b_p_year` ,`instrument` ,`b_p_id` ,`season` ,`validated`)
		VALUES
		('#b_id', '#p_id', '#season', '#p_function', '' , '#season', '0');";
	$b_p = new SQL($b_p,$rel_infos);
	$b_p = $b_p->run();
	
	## IF A SUCESS
	if($b_p) {
		$MSG = array(true, ((isset($saveMSG)&&$saveMSG) ? $lang['people_saved'] : $lang['people_added']));
//		stat_realtime_add($KOMMUNE_ID, $_SESSION['B_ID'], false, false, $_SESSION['UKM_DINSIDE_UID'], $SEASON);
		require_once('UKM/innslag.class.php');
		$band_object_2013 = new innslag($_SESSION['B_ID']);
		$band_object_2013->statistikk_oppdater();

		logIt($_SESSION['B_ID'], 17, $_POST['p_my_function']);
	} else {
		$MSG = array(false, ($saveMSG ? $lang['people_not_saved'] : $lang['people_not_added']));	
	}
	

	## VALIDATE THE BAND
	require_once('include/validation.inc.php');
	validateBand($_SESSION['B_ID']);
?>