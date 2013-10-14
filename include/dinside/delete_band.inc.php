<?php
#			$deletelink = findLink('dinside').'&delete='.$b['b_id'].'&md='.md5($b['b_id'].'-'.$b['b_name']);

## IF ACTUALLY WANTS TO DELETE A VAND
if(isset($_GET['delete']) && isset($_GET['md'])) {
	# COLLECT NAME AND ID FOR SECURITY REASONS
	$b = new SQL("SELECT `b_name`,`b_id`,`b_contact` FROM `smartukm_band` WHERE `b_id` = '#bid'",
				 array('bid'=>$_GET['delete']));
	$b = $b->run('array');
	
	## IF IT IS THE CORRECT BAND (id and hashed name with salt is the same) AND IT BELONGS TO THE CONTACT P
	if($_GET['md'] == md5($b['b_id'].'-'.$b['b_name']) && $b['b_contact'] == $_SESSION['UKM_DINSIDE_UID']) {
		## UPDATE THE BAND TO 99 (DELETED)
		$update = new SQL("UPDATE `smartukm_band`
						  SET `b_status` = '99'
						  WHERE `b_id` = '#bid'",
				 		array('bid'=>$_GET['delete']));
		$update = $update->run();
		# REPORT MSG
		$MSG = array(true, $b['b_name']. ' ble avmeldt!');
//		stat_realtime_avmeld($KOMMUNE_ID, $_GET['delete'], $SEASON);
		require_once('UKM/curl.class.php');
		$CURL = new UKMCURL();
		$CURL->request('http://api.ukm.no/innslag:statistikk_oppdater/'. $_GET['delete']);

		logIt($b['b_id'], 23, $_SERVER['REMOTE_ADDR']);
	} else
		$MSG = array(false,'Kunne ikke melde av ' . $b['b_name']);
} else
	$MSG = array(false,'Kunne ikke melde av ' . $b['b_name']);
?>