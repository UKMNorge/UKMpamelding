<?php
require_once('language/velg_type/language.php');

## IF KOMMUNEID IS NOT SET
# BUG FOUND DURING TESTING. ERROR MESSAGE KEPT IN CASE OF SIMULAR CASE SHOULD OCCUR
if(!isset($KOMMUNE_ID) || $KOMMUNE_ID == 0) {
	$CONTENT = 'Beklager, en feil har oppst&aring;tt. Vennligst pr&oslash;v igjen.';
} else {
	## FINN INFO OM HVA MØNSTRINGEN TILLATER
	$query = new SQL("SELECT `smartukm_band_type`.`bt_id`, `bt_name`, `bt_image`, `bt_deadline`, `name` AS `kommunename`, `smartukm_rel_pl_k`.`pl_id`
			  FROM `smartukm_band_type`
			  JOIN `smartukm_rel_pl_bt` ON `smartukm_rel_pl_bt`.`bt_id` = `smartukm_band_type`.`bt_id`
			  JOIN `smartukm_rel_pl_k` ON `smartukm_rel_pl_k`.`pl_id` = `smartukm_rel_pl_bt`.`pl_id`
			  JOIN `smartukm_kommune` ON `smartukm_kommune`.`id` = `smartukm_rel_pl_k`.`k_id`
			  WHERE `smartukm_kommune`.`id`='#k_id'
			  AND `season` = '#season'",
			  array('k_id'=>$KOMMUNE_ID,'season'=>$SEASON));
	$result = $query->run();

	// ADDED 23.11.2010 - makes subscription possible for the three mandatory band types
	// Opens for suscription even though the local contact person did not register the place
	$band_types_allowed[1] = true;
	$band_types_allowed[2] = true;
	$band_types_allowed[3] = true;
	// EOF ADDED 23.11.2010

	
	## LOOP OG SETT VARIABLER FOR HVA SOM ER TILLATT
	while($row = mysql_fetch_assoc($result)) {
		$band_types_allowed[$row['bt_id']] = $row;
	}
	
	## VELKOMMEN TIL PÅMELDING
	if(!isset($NYTT_INNSLAG)) {
		$CONTENT = '<h1>'.$lang['velkommen'].'</h1>'
				  . '<strong>'. $lang['melderdegnapa'] . $PLACE_NAME .'.</strong><br /><br /> ' . $lang['obsmobil'] . '.'
				  . '<br /><br />'
				  . str_replace('#LINK',findLink('faq'),$lang['harduspm'])
				  . '<br /><br />';
		$STEG = 'kontaktperson';
	## IF USER WANTS TO CREATE ANOTHER BAND WITH HIMSELF AS A CONTACT PERSON
	} else {
		$STEG = 'nytt_innslag';
		$CONTENT = '<h1>'.$lang['nytt_innslag'].'</h1>'
				  . '<strong>'. $lang['melderdegnapa_2'] . $PLACE_NAME .'.</strong><br />';
	}
	
	## CHECK IF DEADLINE IS OUT
	$subscribable = $PLACE_DEADLINE > time();
			  
	## LEGG TIL HEADER FOR VANLIG PÅMELDING
	$CONTENT_ADD1 = #'<h2 style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 1px; color: #000;">'. $lang['velghva'] .'</h2>'
			 '<p style="margin-top: 0px; color: #ff0000;">'. $lang['pameldfrist'] . date('d.m.Y', $PLACE_DEADLINE) .' kl. ' . date('H:i', $PLACE_DEADLINE)
			  .(!$subscribable ? ' - <strong>'.$lang['fristen_ute'].'</strong>' : '')
			  .'</p>'
			  ;
	
	## PRINT MULIGHETER FOR VANLIG PÅMELDING
	$counter_1 = 0;
	$CONTENT_ADD1 .= '<table cellpadding="0" cellspacing="0" border="0" id="subscr_table"><tr>';
	foreach($BANDTYPES['regular'] as $i => $bt) {
		if(!isset($band_types_allowed[$bt['bt_id']]))
			continue;
		$counter_1++;
		$CONTENT_ADD1 .= '<td width="90" height="100" align="center" valign="top">'
				  . ($subscribable ? '<a href="'.findLink($STEG, $bt['ico']).'" style="text-decoration:none; color: #000;">' : '<span style="color: #f52626;">')
				  . '<img width="50" src="http://ico.ukm.no/subscription/'.$bt['ico'].'.png" border="0" alt="'.$bt['title'].'" title="'.$bt['title'].'" />'
				  . '<br />'
				  . $bt['name']
				  . ($subscribable ? '</a>' : '</span>')
				  .'</td>'; 
	}
	$CONTENT_ADD1 .= '</tr></table>';
	
	if($counter_1 ==0) $CONTENT .= '<br /><br /><strong>'.$lang['ingen_tilbud'].'</strong>';
	else	  $CONTENT .= $CONTENT_ADD1;
	
	## SEE IF DEADLINE IS OUT FOR WORK-TYPES
	$subscribable = $PLACE_DEADLINE2 > time();
	## FINN INFO OM MØNSTRINGEN
	$CONTENT_ADD = ''
			  #.'<h3 style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 1px; color: #000; font-size: 20px;">'. $lang['velghvajobbemed'] .'</h3>'
			  .'<p style="margin-top: 0px; color: #ff0000;">'. $lang['pameldfrist'] . date('d.m.Y', $PLACE_DEADLINE2) .' kl. ' . date('H:i', $PLACE_DEADLINE2)
			  .(!$subscribable ? ' - <strong>'.$lang['fristen_ute'].'</strong>' : '')
			  .'</p>';
			  
	## PRINT MULIGHETER FOR VANLIG PÅMELDING
	$counter = 0;
	$CONTENT_ADD .= '<table cellpadding="0" cellspacing="0" border="0" id="subscr_table2"><tr>';
	
	foreach($BANDTYPES['work'] as $i => $bt) {
		if(!isset($band_types_allowed[$bt['bt_id']]))
			continue;
		$counter++;
		$CONTENT_ADD .= '<td width="90" height="100" align="center" valign="top">'
				  . ($subscribable ? '<a href="'.findLink('profilside_enk', $bt['ico']).'" style="text-decoration:none; color: #000;">' :'<span style="color: #f52626;">')
				  . '<img width="50" src="http://ico.ukm.no/subscription/'.$bt['ico'].'.png" border="0" alt="'.$bt['title'].'" title="'.$bt['title'].'" />'
				  . '<br />'
				  . $bt['name']
				  . ($subscribable ? '</a>' : '</span>')
				  .'</td>';
	}
	$CONTENT_ADD .= '</tr></table>';
	
	if($counter > 0) 
		$CONTENT .= $CONTENT_ADD;
}
?>
