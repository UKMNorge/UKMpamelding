<?php
## REQUIRE LANGUAGE FILE
require_once('language/dinside/language.php');
require_once('include/dinside/logon.inc.php');

## IF USER "DELETED" A BAND
if(isset($_GET['delete']))
	require_once('include/dinside/delete_band.inc.php');	
## IF SAVING ITS CONTACT INFOS
elseif(isset($_POST['submitContact'])) 
	require_once('include/dinside/save_contactP.inc.php');
## IF SAVING A SINGLE PERSON BAND
elseif(isset($_POST['submitSingle']))
	require_once('include/sms/create.inc.php');
## IF SAVING A SINGLE PERSON BAND
elseif(isset($_POST['submitSingleUpd']))
	require_once('include/dinside/save_single.inc.php');
	
# IF A MESSAGE IS SENT BY SESSION
if(isset($_SESSION['MSG'])) {
	$MSG = $_SESSION['MSG'];
	unset($_SESSION['MSG']);
}

### SHOW THE CONTENT OF DIN SIDE - USER LOGGED ON
if(isset($_SESSION['UKM_DINSIDE_UID']) && !isset($CONTENT)) {
	## FIND POSSIBLE ERROR MESSAGES
	if(isset($_GET['type']) && $_GET['type'] == 'feilSkift')
		$MSG = array(false, str_replace('#link', 'mailto:'.$SUPPORTMAIL, $lang['skift_failed']));
	
	## FIND CONTACT PERSON INFOS
	$contact = new SQL("SELECT * FROM `smartukm_participant` 
					   WHERE `p_id` = '#pid'",
					   array('pid'=>$_SESSION['UKM_DINSIDE_UID']));
	$contact = $contact->run('array');
	# RIGHT BAR
	$R_BAR = '<div style="float: right; width: 250px; margin-left: 10px;">';
	# CONTACT PERSON INFOS
	/*$R_CONTACT = '<table style="border-left: 1px solid #000; padding-left: 10px; margin-top: 40px;">'
				.'<tr>'
				.'<td>'.$lang['kontaktperson'].'</td>'
				.'<td><img src="/img/edit.png" width="32" border="0"/><br /><span style="font-size: 11px; font-weight: normal; color: #000;">Rediger</span></td>'
				.'</tr>'
				.'</table>';*/
	$R_CONTACT = '<div style="border-left: 1px solid #999; padding-left: 10px; margin-top: 40px;">'
				
				.'<table border="0" width="200">'
				.'<tr>'
				.'<td style="font-size:20px; font-weight:bold;">'.$lang['kontaktperson'].'</td>'
				.'<td width="40" align="center">'
				. '<a href="'.findLink('redigerkontakt',$_SESSION['UKM_DINSIDE_UID']).'" style="text-decoration:none;">'
				.  '<img src="/img/edit.png" width="32" border="0"/>'
				.  '<br />'
				.  '<span style="font-size: 11px; font-weight: normal; color: #000;">Rediger</span>'
				. '</a>'
				.'</td>'
				.'</tr>'
				.'</table>'
				
				.'<strong>'.$contact['p_firstname'].' '.$contact['p_lastname'].'</strong>'
				.'<br /><br />'
				.phone($contact['p_phone'])
				.'<br />'
				.$contact['p_email']
				.'<br /><br />'
				.$contact['p_adress']
				.'<br />'
				.$contact['p_postnumber'] .' '. $contact['p_postplace']
				.'</div>'
				;
				
	$R_BAR .= $R_CONTACT;

	$R_FAQ = 	'<div style="border-left: 1px solid #999; padding-left: 10px; margin-top: 40px;">'
			.'<span style="font-size:20px; font-weight:bold;">'.$lang['whattodo'].'</span>'
			.'<br />'
			.'<div style="font-size:12px; margin-bottom: 20px;">'.$lang['hereswttodo'].'</div>';
	## LOOP ALL FAQ'S
	$i=0;
	foreach($lang['header'] as $header => $content) {
		$i++;
		$R_FAQ .= '<div id="header_'.$i.'" style="margin-bottom: 15px; font-size: 15px; font-weight: bold;">'
			  . '<a href="javascript:showFAQ('.$i.');" style="text-decoration:none;">'.$header.'</a>'
			  . '</div>'
			  . '<div id="detail_'.$i.'" style="display: none; margin: 5px; margin-bottom: 20px;">'.$content.'</div>';
	}
	$R_FAQ .= '</div>';
	$R_BAR .= $R_FAQ;
	
	
#	if($_SERVER['REMOTE_ADDR'] == '84.49.37.206')
		$R_BAR .='<div style="border-left: 1px solid #999; padding-left: 10px; margin-top: 40px;">'
				
				.'<table border="0" width="200">'
				.'<tr>'
				.'<td width="40" align="center">'
				.  '<img src="/img/fb-f.png" width="32" border="0"/>'
				.'</td>'
				.'<td style="font-size:20px; font-weight:bold;">V&aelig;r oppdatert!</td>'
				.'</tr>'
				.'<tr>'
				.'<td colspan="2" style="padding-top: 8px;">UKM finner du p&aring; Facebook ogs&aring;! '
				.'F&oslash;lg oss der for &aring; f&aring; med deg siste nytt. '
				.'<br />'
				.'<br />'
				.'Har du sp&oslash;rsm&aring;l om UKM? '
				.'Legg igjen en veggmelding, og vi svarer s&aring; fort vi kan'
				.'<br />'
				.'<br />'
				.'<a href="http://www.facebook.com/pages/UKM/43974273815" target="_blank">G&aring; til facebook-siden</a>'				
				.'</td>'
				.'</tr>'
				.'</table>'
				
				.'</div>';
	
	
	$R_BAR .= '</div>';

	$CONTENT = $R_BAR;
	## END OF RIGHT BAR

	## SET HEADER
	$CONTENT .= '<h1>'.$lang['dinside'].'</h1>';
	if(isset($MSG) && is_array($MSG)) {
		$clr = $MSG[0] ? '0a6158' : 'f3776f';
		$bkg = $MSG[0] ? '6dc6c1' : 'f69a9b';
		$CONTENT .= '<div style="background: #'.$bkg.'; width:600px; border: 2px solid #'.$clr.'; margin: 20px; font-weight: bold; padding: 4px;" align="center">'.$MSG[1].'</div>';
	}
	$CONTENT .= $lang['text'] .' <br /><br />';

	## SET BAND HEADER	
	$CONTENT .= '<h2 style="margin-bottom: 0px;">'.$lang['dineinnslag'].'</h2>';
				
	## THE DIFFERENT BAND CATEGORIES
	$statuses = array('P&aring;meldte innslag'=>'= 8', 'IKKE p&aring;meldte innslag'=>'< 8');
	foreach($statuses as $title => $demand) {
		## FETCH ALL BANDS OF GIVEN CATEGORY
		$bands = new SQL("SELECT `smartukm_band`.`b_name`,
								 `smartukm_band`.`b_id`,
								 `smartukm_band`.`bt_id`,
								 `smartukm_band`.`b_status`,
								 `smartukm_band`.`b_status_text`,
								 `smartukm_band`.`b_kategori`,
								 `smartukm_band_type`.`bt_name`,
								 `smartukm_place`.`pl_name`,
								 `smartukm_place`.`pl_deadline`,
								 `smartukm_place`.`pl_deadline2`
						 FROM `smartukm_band`
						 LEFT JOIN `smartukm_band_type` ON (`smartukm_band_type`.`bt_id` = `smartukm_band`.`bt_id`)
						 LEFT JOIN `smartukm_rel_pl_b` ON (`smartukm_rel_pl_b`.`b_id` = `smartukm_band`.`b_id`)
						 LEFT JOIN `smartukm_place` ON (`smartukm_place`.`pl_id` = `smartukm_rel_pl_b`.`pl_id`)
						 WHERE `b_contact` = '#contact'
						 AND `b_status` #demand
						 AND `b_status` > 0
						 AND `b_year` = '#season'
						 ORDER BY `b_status` DESC;",
						 array('contact'=>$_SESSION['UKM_DINSIDE_UID'], 'demand'=>$demand, 'season'=>$SEASON));
		$bands = $bands->run();
		
		## IF NO BANDS IN GIVEN CATEGORY
		if(mysql_num_rows($bands) == 0)
			continue;
		
		## START FIELDSET
		$CONTENT .= '<fieldset style="width: 600px;">'
			   .'<legend>'.$title.'</legend>'
			   .'<table cellpadding="2" cellspacing="2" border="0" width="590">';	

		$CONTENT .= '<tr><td colspan="4">';
		
		## IF IT IS NOT SUBSCRIBED BANDS (FROM TEXT), PRINT ALERT
		if(strpos($title, 'IKK') !== false) 
			$CONTENT .= '<img src="img/large_alert.png" width="20" /> '. str_replace('X',mysql_num_rows($bands),$lang['antNESTEN']).'<br /><br />';
		## PRINT OK-TEXT
		else 
			$CONTENT .= '<img src="img/large_check.png" width="20" /> '. str_replace('X',mysql_num_rows($bands),$lang['antOK']).'<br /><br />';
			
		$CONTENT .= '</td></tr>';		
	
		## LOOP ALL BANDS
		$j = 0;
		while($b = mysql_fetch_assoc($bands)) {
			$j++;
			## FIND ICO
			$parts = new SQL("SELECT COUNT(`p_id`) AS `deltakere`
							FROM `smartukm_rel_b_p`
							WHERE `b_id` = '#b_id'
							AND `season` = '#season'",
							array('b_id'=>$b['b_id'], 'season'=>$SEASON));
			$parts = $parts->run('field','deltakere');
			
			# CHECK NUMBER OF PARTICIPANTS AND FIND A CORRECT ICON
			if($parts == 0)
				$ico = '<img src="img/user_0.png" width="32" alt="'.$lang['zeropart'].'" title="'.$lang['zeropart'].'" />';
			elseif($parts == 1) 
				$ico = '<img src="img/user_2.png" width="32" alt="'.$lang['onepart'].'" title="'.$lang['onepart'].'" />';
			else
				$ico = '<img src="img/users.png" width="32" alt="'.str_replace('X',$parts,$lang['multiplepart']).'" title="'.str_replace('X',$parts,$lang['multiplepart']).'" />';
			
			## SET THE DEFAULT-LINKG
			$deletelink = findLink('dinside').'&delete='.$b['b_id'].'&md='.md5($b['b_id'].'-'.$b['b_name']);

			##########################################################
			## FIND DIFFERENT SETS OF ICONS TO THE RIGHT COLUMN 	##
			##########################################################
			## THE BAND DOES NOT PARTICIPATE ANY PLACE
			if(empty($b['pl_name'])) {
				$edit = '';
				$alert = '<a href="javascript:alert(\''.$lang['contact_support'].'\');">'
					   .  '<img src="img/large_alert.png" width="32" border="0" alt="'.$lang['se_status'].'" title="'.$lang['se_status'].'" />'
					   . '</a>';
				$delete = '<a href="mailto:'.$SUPPORTMAIL.'?subject='.$lang['supporttop'].'&body='.str_replace('#ID',$b['b_id'],$lang['supportmsg']).'">'
					   .  '<img src="img/teknisk.png" width="32" border="0" alt="'.$lang['avmeldt'].'" title="'.$lang['avmeldt'].'" />'
					   . '</a>';
			
			## DEADLINE IS OUT AND BAND IS NOT SUBSCRIBED
			} elseif($b['b_status'] < 8 && (
											(in_array($b['bt_id'],array(1,2,3,6)) && $b['pl_deadline'] < time())
											) 
											|| 
											(in_array($b['bt_id'],array(4,7,8,9,10)) && $b['pl_deadline2'] < time())
											) {
				$delete = '<a href="javascript:alert(\''.$lang['deadline_out'].'\');">'
					   .  '<img src="img/large_deadlineout.png" width="32" border="0" alt="'.$lang['se_status'].'" title="'.$lang['se_status'].'" />'
					   . '</a>';
				$alert = '<a href="javascript:alert(\''.$lang['deadline_out'].'\');">'
					   .  '<img src="img/large_alert.png" width="32" border="0" alt="'.$lang['se_status'].'" title="'.$lang['se_status'].'" />'
					   . '</a>';
				$edit = '';
			
			## DEADLINE IS OUT BUT BAND IS SUBSCRIBED
			} elseif($b['b_status'] == 8 && (
											(in_array($b['bt_id'],array(1,2,3,6)) && $b['pl_deadline'] < time())
											) 
											|| 
											(in_array($b['bt_id'],array(4,7,8,9,10)) && $b['pl_deadline2'] < time())
											) {
				$edit = '<a href="javascript:alert(\'Her kommer en rapport\');">'
					   .  '<img src="img/large_view.png" width="32" border="0" alt="'.$lang['se_pamelding'].'" title="'.$lang['se_pamelding'].'" />'
					   . '</a>';
				$edit = '';
				$delete = '<a href="javascript:alert(\''.$lang['pameldt_fristute'].'\');">'
					   .  '<img src="img/large_deadlineout.png" width="32" border="0" alt="'.$lang['se_status'].'" title="'.$lang['se_status'].'" />'
					   . '</a>';
				$alert = '';
					   
			## EVERYTHING IS OK WITH BOTH THE BAND AND THE DEADLINE
			} elseif($b['b_status']==8) {
				$edit = '<a href="'.findLink('skiftinnslag', $b['b_id']).'" alt="'.$lang['se/rediger'].'" title="'.$lang['se/rediger'].'">'
						.'<img src="img/edit.png" width="32" border="0" /></a>';
				$alert = '<a href="javascript:alert(\''.$lang['pameldt_fristinne'].'\');">'
					   .  '<img src="img/large_check.png" width="32" border="0" alt="'.$lang['se_status'].'" title="'.$lang['se_status'].'" />'
					   . '</a>';
				$delete = '<a href="'.$deletelink.'" onclick="return confirm(\''.$lang['sikker_meld_av'].'\');">'
					   .  '<img src="img/large_delete.png" width="32" border="0" alt="'.$lang['meld_av'].'" title="'.$lang['meld_av'].'" />'
					   . '</a>';
					   
			## SOMETHING IS MISSING THE BAND, BUT THE DEADLINE IS STILL OK
			} else {
				$edit = '<a href="'.findLink('skiftinnslag', $b['b_id']).'"><img src="img/edit.png" width="32" border="0" /></a>';
				$alert = '<a href="javascript:alert(\''.$lang['ikkepameldt_fristinne'].'\');">'
					   .  '<img src="img/large_'.($b['b_status']==8?'check':'alert').'.png" width="32" border="0" alt="'.$lang['se_status'].'" title="'.$lang['se_status'].'" />'
					   . '</a>';
				$delete = '<a href="'.$deletelink.'" onclick="return confirm(\''.$lang['sikker_meld_av'].'\');">'
					   .  '<img src="img/large_delete.png" width="32" border="0" alt="'.$lang['meld_av'].'" title="'.$lang['meld_av'].'" />'
					   . '</a>';				   
			}
	
			## ADD THE ROW TO THE TABLE
			$CONTENT .= '<tr>'
					   .'<td rowspan="2" width="40" align="center">'.$ico.'</td>'
					   .'<td><strong>'. (empty($b['b_name']) ? '<em>'.$lang['b_noname'].'</em>' : $b['b_name']).'</strong>'
					   .'</td>'
				   
					   . '<td rowspan="2" width="40" align="center">' . $edit .'</td>'
					 #  . '<td rowspan="2" width="40" align="center">' . $alert .'</td>'
					   . '<td rowspan="2" width="40" align="center">' . $delete .'</td>'
						
					   .'</tr>'
					   .'<tr>'
					   .'<td style="color: #555;">'
					   # IF THE BAND IS TYPE 1, PRINT CATEGORY INSTEAD OF BT NAME
					   . ($b['bt_id']==1 ? ($b['b_kategori'] == 'scene' ? 'Musikk' : ucfirst(utf8_decode($b['b_kategori']))) : $b['bt_name']) .' | '
					   ## IF DOES NOT HAVE A ASSIGNED PLACE, PRINT ERROR, IF NOT PLACE NAME
					   . (empty($b['pl_name']) ?
												'<img src="img/alert.png" width="16" /> Ikke p&aring;meldt noen m&oslash;nstring' 
												: $b['pl_name']
						  )
					   .'</td>'
					   .'</tr>'
					   ## SEPARATOR LINE
					   .($j == mysql_num_rows($bands) ? '' : '<tr><td colspan="4" style="border-bottom: 1px solid #ccc; height: 2px;"></td></tr>');


		}
			$CONTENT .= '</table>'
					   .'</fieldset>';
	}
	
	## LINK TO ADD A BAND AND LOGOUT
	$CONTENT .= '<br /><br />'
			  . '<a href="'.findLink('nytt_innslag').'">'
			  .  '<img src="img/add.png" width="32" style="float: left; margin-right: 5px;" border="0"> '
			  .  '<div style="padding-top: 6px; font-size: 20px;">'.$lang['add_band'].'</div>'
			  . '</a>'
			  . '<br /><br /><br />'
			  . '<a href="?logout=true">Logg ut</a>';

### USER FORGOTTEN PASSWORD
} elseif(isset($_GET['email'])) {
	require_once('include/profilepage_php/pwdforgot.inc.php');
### SHOW THE LOGON OF DIN SIDE - USER NOT LOGGED ON
} else {
	$CONTENT = '<form action="'.findLink('dinside').'" method="post">'
		 .'<table cellpadding="0" cellspacing="0" align="center" style="width: 300px; padding: 5px; margin-top: 0px;">'

	      .'<tr>'
		  .'<td colspan="3" style="font-size: 20px; font-weight: bold; padding-bottom: 10px; text-align: center;">'.$lang['din_side'].'</td>'		  
		  .'</tr>'
		  ## IF TRIED TO LOG ON, BUT PASS IS WRONG
	      .(isset($_POST['logon']) ? 
			  '<tr>'
		 	 .'<td colspan="3" style="font-size: 12px; font-weight: bold; padding-bottom: 10px; text-align: center;">'.$lang['feilpass'].'</td>'		  
		 	 .'</tr>'
			 : ''
			)
		   ## IF TRIED TO LOG ON, BUT E-MAILADDRESS IS NOT A CONTACT P ADDRESS
	      .(isset($_GET['nousers']) ? 
			  '<tr>'
		 	 .'<td colspan="3" style="font-size: 12px; font-weight: bold; padding-bottom: 10px; text-align: center;">'.$lang['nouser'].'</td>'		  
		 	 .'</tr>'
			 : ''
			)
		  .'<tr>'
		  .'<td rowspan="3" width="80">'
		  .'<img src="img/user_2.png" width="64" />'
		  .'</td>'
		  .'</tr>'

		  .'<tr>'
		  .'<td>'.$lang['e-post'].'</td>'		  
		  .'<td><input type="text" name="epost" id="dinsideEpost" /></td>'		  
		  .'</tr>'

		  .'<tr>'
		  .'<td>'.$lang['passord'].'</td>'		  
		  .'<td><input type="password" name="passord" /></td>'		  
		  .'</tr>'
		  
		  .'<tr>'
		  .'<td></td>'
		  .'<td colspan="2" style="padding-left: 20px;"><input type="submit" name="logon" value="'.$lang['logon'].'" /></td>'		  
		  .'</tr>'
		  
		  .'<tr>'
		  .'<td colspan="2" align="center"><br /><a href="javascript:forgottenDinSidePass();" style="font-weight:normal;">Glemt passord?</a></td>'
		  .'</tr>'
		  
		  .'</table>'
		  .'</form>';
}