<?php
require_once('include/password.inc.php');
## DID THE USER ALREADY RECIEVE A PASSWORD?
$log = new SQL("SELECT `log_freetext`
			   	FROM `ukmno_smartukm_log`
				WHERE `log_b_id` = '#b_id'
				AND `log_code` = '#code'",
				array('b_id'=>$_SESSION['B_ID'],
					  'code'=>2));
$log = $log->run();
$l_entry = mysql_fetch_assoc($log);
## CONTACT PERSON UPDATED, MEANING ALREADY RECIEVED PASS
if(mysql_num_rows($log) == 1) {
	# GET PASSWORD HASH FOR SESSION
	$data = new SQL("SELECT `p_password`, `p_email`, `p_firstname`,`p_lastname` FROM `smartukm_participant`
					WHERE `p_id` = '#p_id'",
					array('p_id'=>$l_entry['log_freetext']));
	$data = $data->run('array');
	$pass = $data['p_password'];
	
	
	# GEN PASSWORD	
	$pwdclass = new pwdGen();
	$pass = $pwdclass->newPwd();
	$md = $pwdclass->hash();
	# SAVE PASSWORD
	$update = new SQL("UPDATE `smartukm_participant`
					  SET `p_password` = '#pass'
					  WHERE `p_id` = '#p_id'
					  LIMIT 1;",
					  array('pass'=>$md,
							'p_id'=>$l_entry['log_freetext']));
	$update = $update->run();

	
	
	
	# SET DINSIDE SESSIONS
	$_SESSION['UKM_DINSIDE_UID'] = $l_entry['log_freetext'];
	$_SESSION['UKM_DINSIDE_PAS'] = $md;
## CONTACT PERSON NOT UPDATED, MEANING A NEW ONE
//	$msg = str_replace(array('#USER','#PASS'),array($data['p_email'],$pass), $lang['msg_upd']);
	$msg = str_replace(array('#USER','#PASS'),array($data['p_email'],$pass), $lang['msg_new']);

	sendMail(array('address'=>$data['p_email'],'name'=>$data['p_firstname'].' '.$data['p_lastname']), $lang['subj_upd_contact'], $msg);
} else {
	## FETCH CONTACT INFOS FROM LOG
	$log = new SQL("SELECT `log_freetext`
					FROM `ukmno_smartukm_log`
					WHERE `log_b_id` = '#b_id'
					AND `log_code` = '#code'",
					array('b_id'=>$_SESSION['B_ID'],
						  'code'=>1));
	$log = $log->run();
	$l_entry = mysql_fetch_assoc($log);

	# GEN PASSWORD	
	$pwdclass = new pwdGen();
	$pass = $pwdclass->newPwd();
	$md = $pwdclass->hash();
	# SAVE PASSWORD
	$update = new SQL("UPDATE `smartukm_participant`
					  SET `p_password` = '#pass'
					  WHERE `p_id` = '#p_id'
					  LIMIT 1;",
					  array('pass'=>$md,
							'p_id'=>$l_entry['log_freetext']));
	$update = $update->run();
	
	## FETCH USER DATA
	$data = new SQL("SELECT `p_email`, `p_firstname`,`p_lastname` FROM `smartukm_participant`
					WHERE `p_id` = '#p_id'",
					array('p_id'=>$l_entry['log_freetext']));
	$data = $data->run('array');

	# SET DINSIDE SESSIONS
	$_SESSION['UKM_DINSIDE_UID'] = $l_entry['log_freetext'];
	$_SESSION['UKM_DINSIDE_PAS'] = $md;
	
	## SEND MAIL
	$msg = str_replace(array('#USER','#PASS'),array($data['p_email'],$pass), $lang['msg_new']);
	sendMail(array('address'=>$data['p_email'],'name'=>$data['p_firstname'].' '.$data['p_lastname']), $lang['subj_new_contact'], $msg);
}

?>