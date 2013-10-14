<?php
if(!empty($_GET['email'])) {
	## FIND ALL CONTACT PERSONS WITH THIS E-MAIL ADDRESS
	$qry = new SQL("SELECT `smartukm_participant`.* FROM `smartukm_participant`
				   JOIN `smartukm_band` ON (`smartukm_band`.`b_contact` =  `smartukm_participant`.`p_id`)
				   WHERE `p_email` = '#email'
				   GROUP BY `p_id`
				   ORDER BY `p_id` ASC",
				   array('email'=>$_GET['email']));
	$qry = $qry->run();
	
	## INITIATE CONTENT, MAIL AND SUPPORT-REPORT
	$CONTENT = '<h1 style="font-size: 40px; color:#000;">Glemt passord</h1>';
	$MAIL =  '<br /><strong>Hei!</strong>'
			.'<br />Vi har f&oslash;lgende '.(mysql_num_rows($qry) > 1 ? mysql_num_rows($qry) : '').' kontaktperson'.(mysql_num_rows($qry) > 1 ? 'er' : '').' registrert med din e-postadresse ('.$_GET['email'].').'
			.(mysql_num_rows($qry) > 1 ? '<br />OBS: Alle har n&aring; f&aring;tt nye passord' : '')
			.'<br /><br />';
	$REPORT = '<br />Hei support! Fint om dere kan sl&aring; sammen disse for meg:<br /> ';
	
	## REQUIRE PASSWORD GEN
	require_once('include/password.inc.php');
	
	## LOOP ALL CONTACT PERSONS
	while($r = mysql_fetch_assoc($qry)) {
		## NUMBER OF BANDS
		$bands = new SQL("SELECT COUNT(`b_id`) AS `bands`
						  FROM `smartukm_band`
						  WHERE `b_contact` = '#pid'
						  AND `b_year` = '#season'
						  AND `b_status` > 0
						  AND `b_status` < 9",
						  array('pid'=>$r['p_id'], 'season'=>$SEASON));
		#$CONTENT .= $bands->debug();
		$bands = $bands->run('field','bands');
		
		## GENERATE PASSWORD FROM PATTERN
		$pwd = new pwdGen('ccVV00');
		$txt = $pwd->newPwd();
		$md5 = $pwd->hash();
		
		## UPDATE THE USER
		$upd = new SQL("UPDATE `smartukm_participant`
					   SET `p_password` = '#pass'
					   WHERE `p_id` = '#pid'",
					   array('pass'=>$md5, 'pid'=>$r['p_id']));
		$upd = $upd->run();
		
		## ADD MAIL-TEXT
		$MAIL .= '<strong>'. $r['p_firstname'] .' '. $r['p_lastname'] .'</strong>'
				.' ('.$bands.' innslag i &aring;r)'
				.'<br />'
				.'Brukernavn: '. $r['p_email'] .' Passord: ' . $txt
				#.'<br />DEBUG: '.$r['p_id'] .' (' .$md5.')'
				.'<br /><br />';
		## ADD REPORT-TEXT IN CASE THE USER WANTS A MERGE
		 $REPORT .= $r['p_id'] .'-';
	}
	## IF SEVERAL USERS (MORE THAN TWO), SUGGEST A MERGE
	if(mysql_num_rows($qry) > 1) 
		$MAIL .= '<strong>Vi ser at du har registrert mange brukere.</strong> '
				.'<br />Det vil v&aelig;re lettere for deg om disse er sl&aring;tt sammen til en kontaktperson. '
				.'<br />Vi kan gj&oslash;re dette for deg om du sender en e-post til <a href="mailto:support@ukm.no">support@ukm.no</a> med f&oslash;lgende tekst:'
				.$REPORT;
				
	## ADD SINCERLY
		$MAIL .= '<br / ><br />Mvh<br>UKM Norge<br>support@ukm.no';
	
	## GENERATE CONTENT
	## IF NO CONTACT PERSONS
	if(mysql_num_rows($qry) == 0) {
		header("Location: ". findLink('dinside').'&nousers=true');
		exit();
	## IF ONE CONTACT PERSON
	} elseif(mysql_num_rows($qry) == 1) {
		$CONTENT .= 'Vi har n&aring; sendt deg en e-post med ditt brukernavn og passord';
	} else {
		$CONTENT .= 'Vi fant flere brukere registrert p&aring; din e-postadresse. Vi har sendt deg en liste over brukernavn og passord. '
				   .'<br />I e-posten ligger det ogs&aring; en e-postlenke du kan klikke p&aring; om du vil at vi skal sl&aring; sammen brukerne dine, '
				   .'s&aring; slipper du &aring; huske s&aring; mange passord.';
	}
		$CONTENT .= '<br / ><br />Mvh<br>UKM Norge<br>support@ukm.no'
				   .'<br /><br />'
				   .'<a href="'.findLink('dinside').'">Tilbake til Din Side</a>';
				   
	## SEND MAIL
	#sendMail($to, $subject, $HTMLmessage, $TEXTmessage=false) {
	sendMail(array('address'=>$_GET['email'],'name'=>$r['p_firstname']), 'UKM: Innloggingsinformasjon Din Side', $MAIL);
	#$CONTENT .= '<hr>'.$MAIL;
}
?>