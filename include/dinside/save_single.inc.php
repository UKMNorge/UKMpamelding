<?php
###########################################################
## CALCULATE INFOS NOT GIVEN
	# DOY
	if($_POST['p_age'] != 0) {
		$doy  = (int) date("Y") - (int) $_POST['p_age'];
		$dob  = mktime(0,30,0,1,1,$doy); # timestamp 1st of jan doy @ 00:30
	} else {
		$dob = 0;
	}
	
	### UPDATE PARTICIPANT
	# CREATE INFO ARRAY
	$postalplace = new SQL("SELECT `postalplace` FROM `smartcore_postalplace`
						   WHERE `postalcode` = '#postalcode'",
						   array('postalcode'=>$_POST['p_postalcode']));
	$postalplace = $postalplace->run('field','postalplace');

	$participant_infos = array('p_firstname'=>ucfirst($_POST['p_firstname']),
							  'p_lastname'=>ucfirst($_POST['p_lastname']),
							  'p_dob'=>$dob, # CALCULATED FROM THE GIVEN AGE, SET TO FIRST OF JANUARY
							  'p_email'=>$_POST['p_email'],
							  'p_phone'=>$_POST['p_phone_first'],
							  'postalcode'=>$_POST['p_postalcode'],
							  'postalplace'=>$postalplace, # FROM DATABASE RELATION TO POSTALCODE
							  'p_address'=>ucfirst($_POST['p_address']),
							  'time'=>time(),
							  'P_ID'=>$_POST['person_id']
							  );
	## QUERY TO UPDATE
	$participant = "UPDATE `smartukm_participant` 
					SET `p_firstname` = '#p_firstname',`p_lastname` = '#p_lastname',`p_dob` = '#p_dob',`p_email` = '#p_email',
					`p_phone` = '#p_phone',`p_postnumber` = '#postalcode',`p_postplace` = '#postalplace',
					`p_adress` = '#p_address' 
					WHERE `p_id` = '#P_ID';";
	
	$p_object = new SQL($participant, $participant_infos);	  
	$p_object = $p_object->run();
	
	### UPDATE BAND
	$band_infos = array('b_name'=>ucfirst($_POST['p_firstname']).' '.ucfirst($_POST['p_lastname']),
						'b_id'=>$_POST['band_id']);
	$band = "UPDATE `smartukm_band` 
					SET `b_name` = '#b_name'
					WHERE `b_id` = '#b_id';";
	
	$b_object = new SQL($band, $band_infos);
	$b_object = $b_object->run();

	if(isset($_POST['function']) && is_array($_POST['function']))
		$funksjon = implode(', ', $_POST['function']);
	else
		$funksjon = 'Ikke valgt';
	if(empty($funksjon))
		$funksjon = 'Ikke valgt';
		
	### UPDATE RELATION
	$rel_infos = array('b_id'=>$_POST['band_id'],
					   'p_id'=>$_POST['person_id'],
					   'function'=>$funksjon
					   );
	$relation = "UPDATE `smartukm_rel_b_p`
				 SET `instrument` = '#function'
				 WHERE `b_id` = '#b_id'
				 AND `p_id` = '#p_id'";
	$r_object = new SQL($relation, $rel_infos);	  
	$r_object = $r_object->run();

if(isset($_SESSION['B_ID']))
	logIt($_SESSION['B_ID'], 24);
	
	$MSG = array(true, 'Personen ble oppdatert!');
