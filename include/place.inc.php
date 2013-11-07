<?php
$LEFT = '<div id="positionneur">
		  <h3 class="menuleft">HJELP</h3>
		 </div>
		 <div style="font-size: 13px; padding-left: 4px;">'
		 .'Trenger du hjelp med p&aring;meldingen er vi <a href="'.findLink('faq').'">her for &aring; hjelpe!</a>'
		 .'</div>';
		 
if((isset($_SESSION['UKM_PAM_BROWSER'])&&$_SESSION['UKM_PAM_BROWSER']=='Internet Explorer') || !isset($_SESSION['UKM_PAM_BROWSER'])){
$LEFT .= '<div id="positionneur">
		  <h3 class="menuleft">PROBLEMER?</h3>
		 </div>
		 <div style="font-size: 13px; padding-left: 4px;">'
		 .'Noen sliter dessverre med p&aring;melding i Internet Explorer. Mens vi jobber med &aring; rette problemet kan du installere og pr&oslash;ve p&aring;meldingen i '
		 .'<a href="http://www.mozilla.org/en-US/firefox/fx/#desktop" style="font-weight: bold;">firefox</a>'
		 .'<br /><br />'
		 .'Vi gj&oslash;r alt vi kan for &aring; rette feilen'
		 .'</div>';
}
if(isset($PLACE_ID)) {
	## FIND THE PLACE AND PLACE INFOS
	$qry = "SELECT `pl_name`,`pl_start`,`pl_stop`,`pl_place`, `pl_deadline`, `pl_deadline2` FROM `smartukm_place` WHERE `pl_id` = '".$PLACE_ID."'";
	$res = mysql_query($qry);
	$row = mysql_fetch_array($res);
	
	## SET GLOBAL VARIABLES
	$PLACE_NAME = $row['pl_name'];
	$PLACE_START = $row['pl_start'];
	$PLACE_STOP  = $row['pl_stop'];
	$PLACE_DEADLINE = $row['pl_deadline'];
	$PLACE_DEADLINE2 = $row['pl_deadline2'];

	## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ##
	## LAGT TIL 31.10.2011
/*
	require_once('/home/ukmno/public_html/UKM/pamelding/contact.inc.php');
	require_once('/home/ukmno/public_html/UKM/api/kontakt.class.php');
	$pl_contact = getContact($PLACE_ID, $KOMMUNE_ID);
	$pl_contact = new kontakt($pl_contact['id']);
*/
	require_once('UKM/monstring.class.php');
	$pl = new monstring($PLACE_ID);
	$pl_contact = $pl->hovedkontakt($KOMMUNE_ID, true);
	## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ## !!! ##
	
/*	if($_SESSION['UKM_PAM_BROWSER']!='Internet Explorer' && $pl_contact['picture'] > 0)
		$img = 'http://ukm.no/image.php?id='.$pl_contact['picture'].'&inbig=50';
	else
		$img = 'img/user_1.png';*/
		//http://www.mozilla.org/en-US/firefox/fx/#desktop
	
	if($_GET['steg'] !== 'dinside') {
		## LEFT BAR WITH PLACE INFOS
		$LEFT .= '<div id="positionneur">
					<h3 class="menuleft">M&Oslash;NSTRINGEN</h3>
				  </div>
				  <div style="font-size: 13px; padding-left: 4px;">'
					.'<strong>Starter</strong>: '.date('d.m.Y', $row['pl_start']) .' kl. '. date('H:i', $row['pl_start']).'<br />'
					.'<strong>Slutter</strong>: '.date('d.m.Y', $row['pl_stop']) .' kl. '. date('H:i', $row['pl_stop']).'<br />'
					.'<strong>Sted</strong>: '.$row['pl_place']
					.'<br /><br />'
					.'<a href="http://ukm.no/pl'.$PLACE_ID.'/" target="_blank">Les mer om m&oslash;nstringen</a>
				  </div>';
		## LEFT BAR WITH CONTACT PERSON INFOS
		$LEFT .= '<div id="positionneur">
					<h3 class="menuleft">LOKALKONTAKT</h3>
				  </div>
				  <br clear="all" />
				 <table cellpadding="1" cellspacing="1" width="171" border="0">
				  <tr>
				   <th colspan="2" align="left" style="font-size: 14px; padding-left: 4px;">'.utf8_decode($pl_contact->g('name')).'</th>
				  </tr>
				  <tr>
					<td rowspan="2" align="center"><img src="'.$pl_contact->g('image').'" width="50" /></td>
					<td style="font-size: 13px; padding-left: 4px;">'.phone($pl_contact->g('tlf')).'</td>
				  </tr>
				  <tr>
					<td style="font-size: 13px; padding-left: 4px;"><a href="mailto:'.$pl_contact->g('email').'">Send e-post</a></td>
				  </tr>
				  <tr>
					<td width="40"></td>
					<td></td>
				  </tr>
				 </table>';
		## LEFT BAR WITH SUBSCIBED INFOS
		$LEFT .= '<div id="positionneur">
				<h3 class="menuleft">P&Aring;MELDTE</h3>
			  </div>
			  <div style="font-size: 13px; padding-left: 4px;">
				<a href="http://ukm.no/pl'.$PLACE_ID.'/?pameldte" target="_blank">Se hvem som allerede er p&aring;meldt denne m&oslash;nstringen</a>
			 </div>';
	}
} elseif(isset($p) && (!isset($KOMMUNE_ID) || $KOMMUNE_ID == 0)) {
	## CHANGED 24.01.2011
	$KOMMUNE_ID = $p['p_kommune'];
	$_SESSION['KOMMUNE_ID'] = $p['p_kommune'];
		
	$place = new SQL("SELECT `pl_id` FROM `smartukm_rel_pl_k`
					 WHERE `k_id` = '#kommune'
					 AND `season` = '#season'",
					 array('kommune'=>$p['p_kommune'],
						   'season'=>$SEASON));
	$place = $place->run('field','pl_id');
	## CHANGED 24.01.2011 from one two lines
	$PLACE_ID = $place;
	$_SESSION['PLACE_ID'] = $place;
}

## IF WORKING ON A BAND -SHOW LOG (ONLY TEMP)
if(false) { #isset($_SESSION['B_ID'])) {
	$LEFT .= '<div id="positionneur">
			  <h3 class="menuleft">LOGG</h3>
			 </div>
			 <div style="font-size: 13px; padding-left: 4px;">'
			 .'Alt som er gjort med innslaget er loggf&oslash;rt, <a href="http://pamelding.ukm.no/logg.php?BID='.$_SESSION['B_ID'].'&sec='.md5($_SESSION['B_ID'].'yrdysafe').'" onclick="window.open(this.href,\'LOGG\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=420,height=400,left=430,top=23\'); return false;">klikk her for &aring; se</a>'
			 .'</div>';
}
if(isset($_SERVER['UKM_DINSIDE_UID'])) 
	$LEFT .= '<div id="positionneur">
			  <h3 class="menuleft">LOGG UT</h3>
			 </div>
			 <div style="font-size: 13px; padding-left: 4px;">'
			 .'<a href="?logout=true">Logg ut</a>'
			 .'</div>';
?>