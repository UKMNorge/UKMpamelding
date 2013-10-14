<?php
if(isset($_POST['p_firstname']) && isset($_POST['p_lastname'])) {
	###########################################################
	## CALCULATE INFOS NOT GIVEN
		# DOY
		if($_POST['p_age'] != 0) {
			$doy  = (int) date("Y") - (int) $_POST['p_age'];
			$dob  = mktime(0,30,0,1,1,$doy); # timestamp 1st of jan doy @ 00:30
		} else {
			$dob = 0;
		}
		# CREATE INFO ARRAY
		$participant_infos = array('p_firstname'=>ucfirst($_POST['p_firstname']),
								  'p_lastname'=>ucfirst($_POST['p_lastname']),
								  'p_dob'=>$dob, # CALCULATED FROM THE GIVEN AGE, SET TO FIRST OF JANUARY
								  'p_phone'=>$_POST['p_phone'],
								  'kommune'=>$KOMMUNE_ID, # FROM SUBSCRIPTION SESSION
								  'time'=>time());
		
	############################################################
	## CHECK IF PARTICIPANT ALREADY EXISTS
		if(isset($_POST['p_id']) && is_numeric($_POST['p_id']) && $_POST['p_id'] > 0) {
			$P_ID = $_POST['p_id'];
		} else {
			$p_exists = new SQL("SELECT `p_id` FROM `smartukm_participant` 
								WHERE `p_firstname` = '#p_firstname'
								AND `p_lastname` = '#p_lastname'
								AND `p_phone` = '#p_phone'",
								array('p_firstname'=>$_POST['p_firstname'],
									  'p_lastname'=>$_POST['p_lastname'],
									  'p_phone'=>$_POST['p_phone']));
			$p_exists = $p_exists->run();
		}
		## IF FOUND AN OBJECT, TAKE THE FIRST (AND HOPEFULLY ONLY..)
		if(isset($P_ID) || mysql_num_rows($p_exists) !== 0) {
			if(!isset($P_ID)) {
				$p_exists = mysql_fetch_assoc($p_exists);
				$P_ID = $p_exists['p_id'];
			} else {
				$saveMSG = true; # PRINT SAVE MSG INSTEAD OF ADD-MESSAGE	
			}
			# add the P_ID to the info-array
			$participant_infos = array_merge($participant_infos, array('P_ID'=>$P_ID));
			## QUERY TO UPDATE
			$participant = "UPDATE `smartukm_participant` 
							SET `p_firstname` = '#p_firstname',`p_lastname` = '#p_lastname',`p_dob` = '#p_dob',`p_phone` = '#p_phone'
							WHERE `p_id` = '#P_ID';";
			$p_log_code = 16;
		} else {
			## QUERY TO INSERT
			$participant = "INSERT INTO `smartukm_participant` 
							(`p_id` ,`p_firstname` ,`p_lastname` ,`p_dob` ,`p_email` ,`p_phone` ,`p_kommune` ,`p_postnumber` ,
							 `p_postplace` ,`p_adress` ,`p_password` ,`p_function` ,`p_timestamp` ,`p_about`)
						   VALUES
						   ('' , '#p_firstname', '#p_lastname', '#p_dob', '', '#p_phone','#kommune', '','', '', '', '', '#time', '');";
			$p_log_code = 15;
		}
	
	## CREATE/UPDATE SQL AND INSERT
		$p_object = new SQL($participant, $participant_infos);	  
		$p_object = $p_object->run();
		$P_ID = isset($P_ID) ? $P_ID : mysql_insert_id();
		logIt($_SESSION['B_ID'], $p_log_code, $P_ID);
//		stat_realtime_add($KOMMUNE_ID, $_SESSION['B_ID'], false, false, $P_ID, $SEASON);
		require_once('UKM/curl.class.php');
		$CURL = new UKMCURL();
		$CURL->request('http://api.ukm.no/innslag:statistikk_oppdater/'. $_SESSION['B_ID']);

	## FIX RELATION (DELETE AND INSERT)
		$rel_infos = array('b_id'=>$_SESSION['B_ID'],
								 'p_id'=>$P_ID,
								 'season'=>$SEASON,
								 'p_function'=>$_POST['p_function']);
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
			logIt($_SESSION['B_ID'], 17, $_POST['p_function']);
		} else {
			$MSG = array(false, ($saveMSG ? $lang['people_not_saved'] : $lang['people_not_added']));	
		}
		
		## VALIDATE THE BAND
		require_once('include/validation.inc.php');
		validateBand($_SESSION['B_ID']);
}
?>