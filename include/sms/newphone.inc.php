<?php
	require_once('include/api.smas.inc.php');
	require_once('include/password.inc.php');
	###########################################################
	###########################################################
	##			FIND AND UPDATE PARTICIPANT OBJECT			 ##
	###########################################################
	###########################################################
	logIt($_SESSION['B_ID'], 6, $_POST['p_phone_first']);
	
	## FIND P ID
	$P_ID = new SQL("SELECT `b_contact`
					FROM `smartukm_band`
					WHERE `b_id` = '#b_id'",
					array('b_id'=>$_SESSION['B_ID']));
	$P_ID = $P_ID->run('field','b_contact');
	
	## UPDATE CONTACT P
	$newphone = new SQL("UPDATE `smartukm_participant`
						SET `p_phone` = '#p_phone'
						WHERE `p_id` = '#p_id'",
						array('p_phone'=>$_POST['p_phone_first'],
							  'p_id'=>$P_ID));
	$newphone = $newphone->run();
	## UPDATE BAND
	$newphone = new SQL("UPDATE `smartukm_band`
						SET `b_validatedby` = '#p_phone'
						WHERE `b_id` = '#b_id'",
						array('p_phone'=>$_POST['p_phone_first'],
							  'b_id'=>$_SESSION['B_ID']));
	$newphone = $newphone->run();
	###########################################################
	###########################################################
	##						SEND SMS						 ##
	###########################################################
	###########################################################
	## NUMBER OF SMS SENT TO THIS BAND
	$SMS_sent = new SQL("SELECT COUNT(`log_id`) AS `sent`
						FROM `ukmno_smartukm_log`
						WHERE `log_code` = '5'
						AND `log_b_id` = '#b_id'",
						array('b_id'=>$_SESSION['B_ID']));
	$SMS_sent = $SMS_sent->run('field','sent');
	
	## OK NUMBER OF SMS SENDT, SEND NEW
	if((int) $SMS_sent < 3) {
		$SMS = new pwdGen('cV00Vc');
		$SMS = $SMS->newPwd();
		$_SESSION['SMSpass'] = $SMS;
			$message = str_replace('#code', $SMS, $lang['confirmSMS']);
/*
			require_once('UKM/sms.class.php');
			$SMS = new SMS('pamelding',2);
			$SMS->text($message)->to($_POST['p_phone_first'])->from('UKMNorge')->ok();			
*/

			$smsURL = 'http://www.sveve.no/SMS/SendSMS?user=ukm&msg='.urlencode(utf8_encode($message)).'&to='.$_POST['p_phone_first'].'&from=UKMNorge';
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
									  'logsystem'=>'pameldingNewPhone',
									  'logmessage'=>$message));
			$newLogSQL12->run();
			
		logIt($_SESSION['B_ID'], 5, $SMS);
	} else {
		logIt($_SESSION['B_ID'], 7, $_SERVER['REMOTE_ADDR']);
		$DIE = '<div style="color:#f52626;">'
			  .'<h1 align="center">'
			  .$lang['notsent']
			  .'</h1>'
			  .str_replace('#LINK', 'javascript:waitAndGo('.time().');', $lang['notsent_why'])
			  .'</div>';
	}
?>