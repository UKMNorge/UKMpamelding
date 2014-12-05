<?php
require_once('language/support/language.php');
logIt($_SESSION['B_ID'], 8, $_SERVER['REMOTE_ADDR']);

## IF NOT ISSET WHEN, IT MEANS PAGE IS NOT REFRESHED
if(!isset($_SESSION['when'])) {	
	$day = date('N');  # the day number of today
	$hour = date('H'); # the hour now
	
	## IF MONDAY - THURSDAY, IT IS TOMORROW
	if($day < 5) {
		## BEFORE 9, ANSWER THE SAME DAY
		if($hour < 9)
			$when = $lang['today'] . ' ' . $lang['before12'];
		## BEFORE 15, ANSWER THE SAME DAY
		elseif($hour < 15)
			$when = $lang['today'] . ' ' . $lang['before16'];
		## AFTER 15, ANSWER TOMORROW MORNING
		else
			$when = $lang['tomorrow'] .' '. $lang['before12'];
	## IF FRIDAY, IT IS TODAY OR MONDAY
	} elseif($day == 5) {
		## BEFORE 9, ANSWER THE SAME DAY
		if($hour < 9)
			$when = $lang['today'] . ' ' . $lang['before12'];
		## BEFORE 15, ANSWER THE SAME DAY
		elseif($hour < 15)
			$when = $lang['today'] . ' ' . $lang['before16'];
		## AFTER 15, ANSWER MONDAY MORNING
		else
			$when = $lang['monday'] .' '. $lang['before12'];
	} else {
		$when = $lang['monday'] .' '. $lang['before12'];	
	}
	
	## SEND E-MAIL ALERT TO SUPPORT
	# FETCH BAND INFOS
	$query = new SQL("SELECT `smartukm_participant`.*, `smartukm_band`.`b_id`
					 FROM `smartukm_band`
					 JOIN `smartukm_participant` ON (`smartukm_participant`.`p_id` = `smartukm_band`.`b_contact`)
					 WHERE `smartukm_band`.`b_id` = '#b_id'",
					 array('b_id'=>$_SESSION['B_ID']));
	$band = $query->run('array');
	
	$message = '<h1>SMS-validering feilet for B-ID:'. $_SESSION['B_ID'].'</h1>'
			. '<p><strong>Dette betyr at innslaget ikke har fått SMS med engangskode i løpet av 5 minutter.</strong></p>'
			. '<h3>Dette skjer videre</h3>'
			. '<p>Brukeren har fått beskjed om å sende en SMS til 1963 (motsatt validering). Hvis dette lykkes vil du få en ny e-post fra valideringssystemet med beskjed om at alt er i orden.</p>'
			. '<p><strong>Hvis du <u>ikke</u> får en e-post fra valideringssystemet må du:</strong></p>'
			. '<ol>'
			.  '<li>Sjekke at mobilnummeret nedenfor har en tilknytning til navnet (telefonkatalog, facebook, google osv). Hvis telefonen er registrert på en person med samme etternavn eller andre lignende koblinger er dette ok. Meningen med denne sjekken er å utelukke at dette kan være et mobbe-forsøk</li>'
			.  '<li><strong>Hvis riktig navn og mobilnummer:</strong>Godkjenne innslaget manuelt (<a href="http://ukm.no/wp-admin/network/?page=UKMsupport">Gå til support-modulen</a>)</li>'
			.  '<li><strong>Hvis feil navn:</strong> Putt e-posten i mappen "ikke validert pga feil navn"</li>'
			. '</ol>'
			. '<h3>Informasjon om innslaget</h3>'
			. '<ol>'
			.  '<li><strong>B-ID (innslagsID):</strong> '. $_SESSION['B_ID'] .'</li>'
			.  '<li><strong>Navn:</strong> '.$band['p_firstname'].' '.$band['p_lastname'] .'</li>'
			.  '<li><strong>Svar er lovt innen</strong> ' . $when .'</li>'
			.  '<li><strong>Mobil:</strong> '.$band['p_phone'] .'</li>'
			.  '<li><strong>E-post:</strong> '.$band['p_email'] .'</li>'
			.  '<li><strong>IP-adresse:</strong> '. $_SERVER['REMOTE_ADDR'] .'</li>'
			. '</ol>'
			;

	$automate = "INSERT INTO `smartukm_band_manualvalidate` 
				(`v_id` ,`b_id` ,`v_phone` ,`v_complete` ,`v_time`)
				VALUES
				('' , '".$_SESSION['B_ID']."', '".$band['p_phone']."', 'false',CURRENT_TIMESTAMP);";
	$automate = mysql_query($automate);
	
	require_once('UKM/mail.class.php');
	$epost = new UKMmail();
	$epost->text($message)
		  ->to('support@ukm.no')
		  ->subject('SMS-validering B-ID: '.$band['b_id'])
		  ->ok();
	
## PAGE IS REFRESHED, DO NOT UPDATE THE PAGE SHOWN TO THE USER
} else {
	$when = $_SESSION['when'];	
}

if(empty($_SESSION['B_ID'])||!isset($_SESSION['B_ID'])){	
	$CONTENT = '<h1>Noen ganger kan dessverre teknologien svikte, men dette ordner vi! :-)</h1>'
			   .'<strong>Hva skjer videre?</strong>'
			   .'<br /><br />'
			   .'1) Du kan pr&oslash;ve &aring; melde deg p&aring; i en annen nettleser (for eksempel <a href="http://www.mozilla.org/en-US/firefox/fx/#desktop">firefox</a>)'
			   .'<br />'
			   .'2) Du kan sende en e-post til <a href="mailto:support@ukm.no">support@ukm.no</a> s&aring; hjelper vi deg videre.'
			   .'<br /><br />'
			   .'Vi anbefaler at du pr&oslash;ver steg 1 f&oslash;rst'
			   .'<br /><br />'
			   .'<strong>'.$lang['why'].'</strong>'
			   .'<br />'
			   .$lang['thatswhy']
			   .'<br /><br />'
			   .$lang['from'];	
} else {
	$CONTENT = '<h1>'.$lang['failed'].'</h1>'
			   .'<strong>'.$lang['whattodo'].'</strong>'
			   .'<br /><br />'
			   .str_replace(array('#SUPPORT','#BANDID', '#WHEN'),array(1963, $_SESSION['B_ID'], $when), $lang['list'])
			   .'<br /><br />'
			   .$lang['whatnottodo']
			   .'<br /><br />'
			   .'<strong>'.$lang['why'].'</strong>'
			   .'<br />'
			   .$lang['thatswhy']
			   .'<br /><br />'
			   .$lang['from'];
}
?>
