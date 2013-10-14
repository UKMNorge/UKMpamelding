<?php
	###########################################################
	###########################################################
	##					PARTICIPANT OBJECT					 ##
	###########################################################
	###########################################################
	
	###########################################################
	## CALCULATE INFOS NOT GIVEN
		# DOY
		if(isset($_POST['p_age']) && $_POST['p_age'] != 0) {
			$doy  = (int) date("Y") - (int) $_POST['p_age'];
			$dob  = mktime(0,30,0,1,1,$doy); # timestamp 1st of jan doy @ 00:30
		} else {
			$dob = 0;
		}
		# POSTALPLACE
		$postalplace = new SQL("SELECT `postalplace` FROM `smartcore_postalplace`
							   WHERE `postalcode` = '#postalcode'",
							   array('postalcode'=>$_POST['p_postalcode']));
		$postalplace = $postalplace->run('field','postalplace');
		# CREATE INFO ARRAY
		$participant_infos = array('p_firstname'=>ucfirst($_POST['p_firstname']),
								  'p_lastname'=>ucfirst($_POST['p_lastname']),
								  'p_dob'=>$dob, # CALCULATED FROM THE GIVEN AGE, SET TO FIRST OF JANUARY
								  'p_email'=>$_POST['p_email'],
								  'p_phone'=>$_POST['p_phone_first'],
								  'kommune'=>$KOMMUNE_ID, # FROM SUBSCRIPTION SESSION
								  'postalcode'=>$_POST['p_postalcode'],
								  'postalplace'=>$postalplace, # FROM DATABASE RELATION TO POSTALCODE
								  'p_address'=>ucfirst($_POST['p_address']),
								  'time'=>time());
		
	############################################################
	## CHECK IF PARTICIPANT ALREADY EXISTS
		$p_exists = new SQL("SELECT `p_id` FROM `smartukm_participant` 
							WHERE `p_firstname` = '#p_firstname'
							AND `p_lastname` = '#p_lastname'
							AND `p_phone` = '#p_phone'",
							array('p_firstname'=>$_POST['p_firstname'],
								  'p_lastname'=>$_POST['p_lastname'],
								  'p_phone'=>$_POST['p_phone_first']));
		$p_exists = $p_exists->run();
		
		## IF FOUND AN OBJECT, TAKE THE FIRST (AND HOPEFULLY ONLY..)
		if(mysql_num_rows($p_exists) !== 0) {
			$p_exists = mysql_fetch_assoc($p_exists);
			$P_ID = $p_exists['p_id'];
			# add the P_ID to the info-array
			$participant_infos = array_merge($participant_infos, array('P_ID'=>$P_ID));
			## QUERY TO UPDATE
			$participant = "UPDATE `smartukm_participant` 
							SET `p_firstname` = '#p_firstname',`p_lastname` = '#p_lastname',`p_dob` = '#p_dob',`p_email` = '#p_email',
							`p_phone` = '#p_phone',`p_kommune` = '#kommune',`p_postnumber` = '#postalcode',`p_postplace` = '#postalplace',
							`p_adress` = '#p_address' 
							WHERE `p_id` = '#P_ID';";
			$p_log_code = 2;
		} else {
			## QUERY TO INSERT
			$participant = "INSERT INTO `smartukm_participant` 
							(`p_id` ,`p_firstname` ,`p_lastname` ,`p_dob` ,`p_email` ,`p_phone` ,`p_kommune` ,`p_postnumber` ,
							 `p_postplace` ,`p_adress` ,`p_password` ,`p_function` ,`p_timestamp` ,`p_about`)
						   VALUES
						   ('' , '#p_firstname', '#p_lastname', '#p_dob', '#p_email', '#p_phone','#kommune', '#postalcode',
							'#postalplace', '#p_address', '', '', '#time', '');";
			$p_log_code = 1;
		}
	
	## CREATE/UPDATE SQL AND INSERT
		$p_object = new SQL($participant, $participant_infos);	  
		$p_object = $p_object->run();
		$P_ID = isset($P_ID) ? $P_ID : mysql_insert_id();
		  
	###########################################################
	###########################################################
	##						BAND OBJECT					 	 ##
	###########################################################
	###########################################################
	
	###########################################################
	## CALCULATE INFOS NOT GIVEN
		$bt_id = getBTIDfromImage($_GET['type']);
		## CATEGORY IS SET ONLY FOR BT_ID == 1, BUT JUST IN CASE WE CALCULATE IT HERE ANYWAYS
		$kategori = ($_GET['type'] == 'scene') ? 'musikk' : (($_GET['type'] == 'annet') ? 'annet på scenen' : $_GET['type']);
		## BAND NAME IS EMPTY IF STANDARD BAND, IF NOT IT IS THE NAME OF THE CONTACT PERSON
		# IF CONTACT P IS NOT THE PARTICIPANT,  THIS IS SAVED IN sms_enk.php
		$b_name = (in_array($_GET['type'],$WORK) ? 
								  				ucfirst($_POST['p_firstname']).' '.ucfirst($_POST['p_lastname']) 
												 : '');
		## HAPPENS ONLY IF THE BAND IS EDITED AND SINGLE-PERSON
		if(isset($_POST['contact_id'])) {
			$status = 8;
			$b_contact = $_POST['contact_id'];
		} else {
			$b_contact = $P_ID;
			$status = 0;
		}
		
	## CREATE INFO ARRAY
		$band_infos = array('b_contact'=>$b_contact, # FROM PARTICIPANT INSERT/UPDATE ABOVE
							'season'=>$SEASON, # FROM DATABASE
							'bt_id'=>$bt_id, # FROM GET TYPE IN URL
							'time'=>time(),
							'kategori'=>($bt_id==1 ? $kategori : ''), # IF SCENE (BT_ID==1), GIVE CATEGORY
							'kommune'=>$KOMMUNE_ID,
							'b_name'=>$b_name, #FROM CALCULATION ABOVE
							'status'=>$status, #FROM CALCULATION ABOVE
							'p_phone'=>$_POST['p_phone_first'],
							'b_description'=>(isset($_POST['b_description']) ? $_POST['b_description'] : ''));
	# QUERY TO INSERT
		$band = "INSERT INTO `smartukm_band` 
				(`b_id` ,`b_name` ,`b_contact` ,`b_year` ,`b_description` ,`b_password` ,`bt_id` ,`b_status` ,`b_subscr_time` ,
				 `b_kategori` ,`b_sjanger` ,`b_kommune` ,`b_nb_part` ,`b_status_text` ,`b_validatedby`, `b_season`)
				VALUES
				('' , '#b_name', '#b_contact', '#season', '#b_description', '', '#bt_id', '#status', '#time', '#kategori', '', '#kommune', '0', '', '#p_phone', '#season');";
	
	## CREATE SQL AND INSERT
		$b_object = new SQL($band, $band_infos);	  
		$b_object = $b_object->run();
		$B_ID = mysql_insert_id();
	
		# LOG PARTICIPANT CREATED
		logIt($B_ID, $p_log_code, $P_ID);
		require_once('UKM/innslag.class.php');
		$band_object_2013 = new innslag($B_ID);
		$band_object_2013->statistikk_oppdater();
//		stat_realtime_add($KOMMUNE_ID, $B_ID, $bt_id, ($bt_id==1?$kategori:''), 0, $SEASON);

	
		###########################################################
		##				TECHNICAL DEMANDS OBJECT				 ##
		###########################################################
		$tech = "INSERT INTO `smartukm_technical` 
				(`td_id` ,`pl_id` ,`b_id` ,`td_demand` ,`td_konferansier`)
				VALUES
				('' , '#pl_id', '#b_id', '', '');";
		$tech = new SQL($tech, 
						array('pl_id'=>$PLACE_ID,
							  'b_id'=>$B_ID));
		$tech->run();
		
		###########################################################
		##					 RELATION TO PLACE					 ##
		###########################################################
		$pl_b = "INSERT INTO `smartukm_rel_pl_b`
				(`pl_id` ,`b_id` ,`season`)
				VALUES
				('#pl_id', '#b_id', '#season');";
		$pl_b = new SQL($pl_b, 
						array('pl_id'=>$PLACE_ID,
							  'b_id'=>$B_ID,
							  'season'=>$SEASON));
		$pl_b->run();
		
		# LOG BAND CREATED
		logIt($B_ID, 3, $PLACE_ID);
		
		## LOG IF THERE IS A BUG
		if(!isset($KOMMUNE_ID))
			logIt($B_ID, 34);
		else
			logIt($B_ID, 33);
		
		
	###########################################################
	###########################################################
	##	BAND - PARTICIPANT OBJECT IF CONTACT P PARTICIPATES	 ##
	###########################################################
	###########################################################
		## IF IT IS A SINGLE PERSON, THE FUNCTION IS GIVEN IN CHECKBOXES/HIDDEN ARRAY
		# CALCULATE THE FUNCTION
		if(isset($_POST['function']) && is_array($_POST['function'])) {
			$_POST['p_function'] = implode(', ', $_POST['function']);	
			if(empty($_POST['p_function']))
				$_POST['p_function'] = 'Ikke valgt';
		} elseif(isset($_POST['function']) && empty($_POST['function']))
			$_POST['p_function'] = 'Ikke valgt';
		## IF FUNCTION WAS SET, BUT NOTHING SELECTED, WE FAKE IT	
		if(!isset($_POST['p_function']) && !isset($_POST['function']))
			$_POST['p_function'] = 'Ikke valgt';
		## IF THE CONTACT P PARTICIPATES IN THE BAND, INSERT RELATIONS
		if(isset($_POST['p_function']) && !empty($_POST['p_function'])) {
			$b_p = "INSERT INTO `smartukm_rel_b_p`
					(`b_id` ,`p_id` ,`b_p_year` ,`instrument` ,`b_p_id` ,`season` ,`validated`)
					VALUES
					('#b_id', '#p_id', '#season', '#p_function', '' , '#season', '0');";
			$b_p = new SQL($b_p,
						   array('b_id'=>$B_ID,
								 'p_id'=>$P_ID,
								 'season'=>$SEASON,
								 'p_function'=>$_POST['p_function']));
			$b_p = $b_p->run();
			logIt($B_ID, 4, $_POST['p_function']);
			stat_realtime_add($KOMMUNE_ID, $B_ID, $bt_id, ($bt_id==1?$kategori:''), $P_ID, $SEASON);

		}
		## HAPPENS ONLY IF THE BAND IS STANDARD-BAND
		if(!isset($_POST['contact_id']) && !isset($_SESSION['SMSpass'])) {
			require_once('include/api.smas.inc.php');
			require_once('include/password.inc.php');
			###########################################################
			###########################################################
			##						SEND SMS						 ##
			###########################################################
			###########################################################
			$SMS = new pwdGen('cV00Vc');
			$SMS = $SMS->newPwd();
			$_SESSION['SMSpass'] = $SMS;
				$message = str_replace('#code', $SMS, $lang['confirmSMS']);
				$smsURL = 'http://www.sveve.no/SMS/SendSMS?user=ukm&msg='.urlencode(utf8_encode($message)).'&to='.$_POST['p_phone_first'].'&from=UKMNorge';
				$APIres = new APIcall('SMSlog', array('to'=>$_POST['p_phone_first'], 'message'=>urlencode($message),'from'=>'UKMNorge'));
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $smsURL);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$res = curl_exec($curl);

				// NYTT LOGG-SYSTEM HØSTEN 2012
				$newLogSQL12 = "INSERT INTO `log_sms_system`
					(`log_from` ,`log_to` ,`log_system` ,`log_message`)
					VALUES
					('#logfrom', '#logto', '#logsystem', '#logmessage');";
				$newLogSQL12 = new SQL($newLogSQL12,
									array('logfrom'=>'UKMNorge',
										  'logto'=>$_POST['p_phone_first'],
										  'logsystem'=>'pameldingUKM',
										  'logmessage'=>$message));
				$newLogSQL12->run();
			
			
			logIt($B_ID, 5, $SMS);
		}
	if(in_array($kategori, $WORK))
		$MSG = array(true, 'Personen ble lagt til');
	else 
		$MSG = array(true, 'Innslaget ble lagt til');
	
	$_SESSION['B_ID'] = $B_ID;
?>