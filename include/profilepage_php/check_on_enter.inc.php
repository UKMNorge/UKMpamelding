<?php
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
## POSSIBLE BUG: THIS HAS NEVER BEEN TESTED!
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
## CHECK IF THE BAND IS VALIDATED
	if(isset($_SESSION['B_ID'])) {
		$validated = new SQL("SELECT `b_status`
						 FROM `smartukm_band`
						 WHERE `b_id` = '#b_id'",
						 array('b_id'=>$_SESSION['B_ID']));
		$validated = $validated->run('field','b_status');
	} elseif(!isset($_SESSION['B_ID'])) {
		$report = '<h3>SESSION</h3>' . var_export($_SESSION, true)
				. '<h3>GET</h3>' . var_export($_GET, true)
				. '<h3>POST</h3>' . var_export($_POST, true)
				. '<h3>SERVER</h3>' . var_export($_SERVER, true)
				;
		die('<h1>Beklager, en feil har oppst&aring;tt!</h1>'
			.'For at vi skal hjelpe deg m&aring; du sende teksten p&aring; denne siden til '
			.'<a href="mailto:support@ukm.no?subject=Mangler_BID-feil">support@ukm.no</a>'
			.'<hr />'
			. $report
			.'<hr />');
	} else 
		$validated = 0;
		
## IF IT IS NOT VALIDATED, SEND TO SMS-PAGE
	if($validated == 0) {
		logIt($_SESSION['B_ID'], 11, $_SERVER['REMOTE_ADDR']);
		$link = findLink('sms', $_GET['type']);
		$link .= (strpos($link, '?') ? '&' : '?') . 'wrong=still';
		header("Location: " . $link);
		exit();
	}
?>