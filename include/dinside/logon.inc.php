<?php

## IF LOGOUT
if(isset($_GET['logout'])) {
	unset($_SESSION['UKM_DINSIDE_UID']);
	unset($_SESSION['UKM_DINSIDE_PAS']);
}

## IF USER WANTS TO SIGN IN (POST ISSET)
if(isset($_POST['epost'])) {
	## CREATE MD5-CHECK
	$md = md5('ukmno' . $_POST['passord'] . 'padjfoaifiASDAosjdo3neslk5435wfnlADSFlascnjuon.kzdj342filjfsodsn');
	#$md = md5($_POST['passord']);
	$qry = new SQL("SELECT `p_id`,`p_kommune` FROM `smartukm_participant`
				   JOIN `smartukm_band` ON (`smartukm_band`.`b_contact` = `smartukm_participant`.`p_id`)
				   WHERE `p_email` = '#email'
				   AND `p_password` = '#password'
				   GROUP BY `smartukm_participant`.`p_id`",
				   array('email'=>$_POST['epost'],
						  'password'=>$md));
	$qry = $qry->run();
	## IF FOUND MORE THAN ONE USER THERE'S A BUG!
	if(!$qry || mysql_num_rows($qry) > 1)
	$CONTENT = '<h1 style="color: #000; font-size: 40px;">'.$lang['beklager_feil'].'</h1>'
			. str_replace('#MAILTO','mailto:support@ukm.no?subject=Flerregistrert passord',$lang['multiple_user']) .' <br /><br /><br />';
	
	## IF FOUND THE ONLY USER
	elseif(mysql_num_rows($qry) == 1) {
		$p = mysql_fetch_assoc($qry);
		$_SESSION['UKM_DINSIDE_UID'] = $p['p_id'];
		$_SESSION['UKM_DINSIDE_PAS'] = $md;
		$_SESSION['KOMMUNE_ID'] 	 = $p['p_kommune'];
		
		$place = new SQL("SELECT `pl_id` FROM `smartukm_rel_pl_k`
						 WHERE `k_id` = '#kommune'
						 AND `season` = '#season'",
						 array('kommune'=>$p['p_kommune'],
							   'season'=>$SEASON));
		$place = $place->run('field','pl_id');
		$_SESSION['PLACE_ID'] = $place;
	}
	unset($md);
}
?>