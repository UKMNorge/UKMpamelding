<?php
### REQUIRE LANGUAGEFILES
if(!file_exists('language/profilepage_std/language_'.$_GET['type'].'.php')) {
	$CONTENT = 'Beklager, en spr&aring;kfil mangler, s&aring; siden kan dessverre ikke vises';
} else {
	require_once('language/profilepage_std/language_'.$_GET['type'].'.php');
	
	##################################################################################
	## 			DO A BAND VALIDATION CHECK BEFORE ENTERING PROFILE PAGE				##
	##################################################################################
	### IF SMS-CODE IS WRONG, SEND BACK TO SMS-PAGE
	if(isset($_POST['SMScode']) && $_POST['SMScode'] !== $_SESSION['SMSpass'])
		require_once('include/profilepage_php/sms_wrong.inc.php');
	### IF SMS-CODE IS CORRECT, VALIDATE BAND, SEND E-MAIL AND LOGON TO DIN SIDE
	elseif(isset($_POST['SMScode']) && $_POST['SMScode'] === $_SESSION['SMSpass']) {
		require_once('include/profilepage_php/sms_correct.inc.php');
	### IF JUST ENTERED PAGE WITHOUT SMS-CODE
	} else
		require_once('include/profilepage_php/check_on_enter.inc.php');
	
	##################################################################################
	##################################################################################
	## 						PERFORM SAVES AND DELETES								##
	##################################################################################
	##################################################################################
	## CHECK IF WE GOT THE BAND NAME, IN THAT CASE WE NEED TO SAVE THESE INFOS
	if(isset($_POST['b_name']))
		require_once('include/profilepage_php/save_profilepage.inc.php');
	
	## IF USER CLICKED A BUTTON ON PROFILEPAGE THE WHOLE FORM IS SENDT
	## CHECK WHAT THE USER WANTS TO DO, AND REQUIRE ACTION-FILE
	if(isset($_POST['theGo_input']) && !empty($_POST['theGo_input'])) {
		switch($_POST['theGo_input']) {
			case 'people_add':				$file = 'people_add';				break;	# ADD A PERSON
			case 'people_delete':			$file = 'people_delete';			break;	# DELETE A PERSON
			case 'title_add':				$file = 'title_add';				break;	# ADD A TITLE
			case 'goToYourPage':			$file = 'goto_your_page';			break;	# GO BACK TO DIN SIDE
			
			case 'title_scene_delete':		$file = 'title_delete_scene';		break;	# DELETE A MUSIC-TITLE 		*1
			case 'title_teater_delete':		$file = 'title_delete_scene';		break;	# DELETE A THEATRE-TITLE	*1
			case 'title_annet_delete':		$file = 'title_delete_scene';		break;	# DELETE A OTHER-TITLE		*1
			case 'title_video_delete':		$file = 'title_delete_video';		break;	# DELETE A VIDEO-TITLE
			case 'title_dans_delete':		$file = 'title_delete_dans';		break;	# DELETE A DANCE-TITLE
			case 'title_utstilling_delete':	$file = 'title_delete_utstilling';	break;	# DELETE A EXHIBITION-TITLE
			case 'title_matkultur_delete':	$file = 'title_delete_matkultur';	break;	# DELETE A FOOD-TITLE
			
			# *1) ALL TITLES STORED IN SAME TABLE, USE THE SAME FILE
		}
		## DO ACTUALLY REQUIRE THE ACTION-FILE
		require_once('include/profilepage_php/'.$file.'.inc.php');
	}
	
	
	#################################################################################
	## 						SWITCH THE DIFFERENT SAVES								##
	##################################################################################	
	## IF A PERSON IS TO BE ADDED
	if(isset($_POST['people_save']))
		require_once('include/profilepage_php/people_save.inc.php');
	elseif(isset($_POST['p_my_function'])) 
		require_once('include/profilepage_php/people_save_me.inc.php');
	## IF ADDING A TITLE
	# VIDEO-TITLE
	elseif(isset($_POST['title_save_video']))
		require_once('include/profilepage_php/title_save_video.inc.php');
	# EXHIBITION-TITLE
	elseif(isset($_POST['title_save_utstilling']))
		require_once('include/profilepage_php/title_save_utstilling.inc.php');
	# MUSIC-/SCENE-TITLE
	elseif(isset($_POST['title_save_scene']))
		require_once('include/profilepage_php/title_save_scene.inc.php');
	# DANCE-TITLE
	elseif(isset($_POST['title_save_dans']))
		require_once('include/profilepage_php/title_save_dans.inc.php');
	# FOOD-TITLE
	elseif(isset($_POST['title_save_matkultur']))
		require_once('include/profilepage_php/title_save_matkultur.inc.php');


	#################################################################################
	## 							COLLECT BAND INFOS									##
	##################################################################################
	## FIND ALL DETAILS OF BAND, OR DIE
		$BAND = new SQL("SELECT 
						`smartukm_band`.*,
						`smartukm_participant`.`p_firstname` AS `c_firstname`,		
						`smartukm_participant`.`p_lastname` AS `p_lastname`,		
						`smartukm_participant`.`p_phone` AS `p_phone`,	
						`smartukm_technical`.*
						FROM `smartukm_band`
						JOIN `smartukm_participant` ON (`smartukm_band`.`b_contact` = `smartukm_participant`.`p_id`)
						JOIN `smartukm_technical` ON (`smartukm_band`.`b_id` = `smartukm_technical`.`b_id`)
						WHERE `smartukm_band`.`b_id` = '#b_id';",
						array('b_id'=>$_SESSION['B_ID']));
		$BAND = $BAND->run('array');
		
		## BAND FETCH FAILED, DIE (ACTUAL DIE IS SET A BIT LATER, BUT THIS INITIATES IT)
		if(!$BAND) {
			logIt($_SESSION['B_ID'], 12);
			$CONTENT = '<h1 style="color: #000;">'.$lang['beklager'].'</h1>'
					  .'<span style="color: #f52626; font-weight:bold;">'. $lang['sorry_could_not_find_band'] .'</span>';
		}
	
	## FIND A RELATION TO A PLACE OR DIE (ACTUAL DIE IS SET A BIT LATER, BUT THIS INITIATES IT)
	# IF CONTENT ISSET ALREADY, IT DID NOT FIND THE BAND
		if(!isset($CONTENT)) {
			$PLACERELATION = new SQL("SELECT `pl_id` FROM `smartukm_rel_pl_b`
									 WHERE `b_id` = '#b_id' AND `season` = '#season'",
									 array('b_id'=>$BAND['b_id'], 'season'=>$SEASON));
			$PLACERELATION = $PLACERELATION->run('array');
			if(!$PLACERELATION) {
				logIt($_SESSION['B_ID'], 13);
				$CONTENT = '<h1 style="color: #000;">'.$lang['beklager'].'</h1>'
						  .str_replace('#LINK', findLink('din_side'), '<span style="color: #f52626; font-weight:bold;">'. $lang['sorry_could_not_place_rel'] .'</span>');
		
			}
		}
		
	##################################################################################
	## 							START PROFILE PAGE									##
	##################################################################################
	## ONLY IF THERE IS NO DIES INITIATED ABOVE
	if(!isset($CONTENT)) {
		## HEADER
		$CONTENT = '<table width="100%" cellpadding="5" cellspacing="0">'
				   .'<tr>'
				   .'<td width="*%">'
				   .'<h1 style="margin: 2px;">'
					   .$lang['profilside_for'] . " "
					# IF BAND NAME IS EMPTY, SAY SO
					   .(empty($BAND['b_name']) ? $lang['ditt_nye_innslag']: $BAND['b_name'])
				   .'</h1>'
				   .'</td>'
				   .'<td width="150" style="border: 1px solid #ccc; color: #666;" align="center">'
				   .'<strong>Kontaktperson:</strong><img src="img/user_0.png" width="16" style="float:left; margin:0px; margin-right: 2px;" /> <br clear="all" />'
				   .$BAND['c_firstname']
				   .'</td>'
				   .'</tr>'
				   .'</table>';
		
		## IF SOMETHING HAS HAPPENED (SAVE OR DELETE)
		if(isset($MSG) && is_array($MSG)) {
			$clr = $MSG[0] ? '0a6158' : 'f3776f';
			$bkg = $MSG[0] ? '6dc6c1' : 'f69a9b';
			$CONTENT .= '<div style="background: #'.$bkg.'; border: 2px solid #'.$clr.'; margin: 20px; font-weight: bold; padding: 4px;" align="center">'.$MSG[1].'</div>';
		}
				   
		## FORM START		   
		$CONTENT .= '<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" id="profilside" name="profilside" enctype="application/x-www-form-urlencoded">';
		
		##################################################################################
		## 								 BAND INFOS										##
		##################################################################################
		## IF SOMETHING IS MISSING IN THE BAND 
		if(!empty($BAND['b_status_text']))
			$CONTENT .= '<fieldset>'
			   .'<legend style="color: #f3776f;">'.$lang['missing'].'</legend>'
			   . nl2br($BAND['b_status_text'])
			   .'</fieldset>';
			   
		#if($_SERVER['REMOTE_ADDR'] == '84.49.37.206') {
			$test_log = new SQL("SELECT `log_id` 
								 FROM `ukmno_smartukm_log`
								 WHERE `log_b_id` = '#bid'
								 AND `log_code` = '34'",
								 array('bid'=>$BAND['b_id']));
			$test_log = $test_log->run();
			## FOUND A LOG ENTRY, INDICATING BUG
			if(mysql_num_rows($test_log) !== 0) {
				$test_log_again = new SQL("SELECT `log_id` 
					 FROM `ukmno_smartukm_log`
					 WHERE `log_b_id` = '#bid'
					 AND `log_code` = '32'",
					 array('bid'=>$BAND['b_id']));
				$test_log_again = $test_log_again->run();
				## IF DID NOT FIND A BUG
				if(mysql_num_rows($test_log_again) == 0)
					$CONTENT .= '<fieldset>'
						   .'<legend>Lokalm&oslash;nstring</legend>'
						   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
						   .'<tr>'
						   .'<td width="350">'
						 #  .'<span class="font20"></span>'
						 #  .'<br />'
						   .'<span class="font11">Skriv inn hvilken lokalm&oslash;nstring du skal delta p&aring; (kommune / bydel)</span>'
						   .'</td>'
						   .'<td>'
						   .'<input type="text" name="b_kommune" class="inputBoks" onkeyup="validate(\'b_kommune\');" onfocus="validate(\'b_kommune\');" onblur="validate(\'b_kommune\');" id="toval_b_kommune" style="width:270px;" value="" />'
						   .validate('b_kommune','twoletters')
						   .'</td>'
						   .'</tr>'
						   .'</table>'				   
						   .'</fieldset>';
			}
		#}
		
		## FIRST FIELDSET
		$CONTENT .= '<fieldset>'
				   .'<legend>'.$lang['infos'].'</legend>'
				   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
				   
		# WARNING-TEXT
				   .'<tr><td colspan="2" style="font-weight:bold;"> '. $lang['obs'] .'</td></tr>'
				   
		# SPACER
				   .'<tr><td colspan="2">&nbsp;  </td></tr>'
				   
		# NAME OF GROUP
				   .'<tr>'
				   .'<td width="350">'
				   .'<span class="font20">'. $lang['group'] .'</span>'
				   .'<br /><span class="font11">'.$lang['group-help'].'</span>'
				   .'</td>'
				   .'<td>'
				   .'<input type="text" name="b_name" class="inputBoks" onkeyup="validate(\'b_name\');" onfocus="validate(\'b_name\');" onblur="validate(\'b_name\');" id="toval_b_name" style="width:270px;" value="'.$BAND['b_name'].'" />'
				   .validate('b_name','threeletters')
				   .'</td>'
				   .'</tr>'
				   
	   	# DISPLAY GENRE IF NOT A EXHIBITION OR FOOD-BAND
				  . ($BAND['bt_id'] != 3 && $BAND['bt_id'] != 6 ?
						'<tr>'
					   .'<td width="350">'
					   .'<span class="font20">'. $lang['genre'] .'</span>'
					   .'<br /><span class="font11">'.$lang['genre-help'].'</span>'
					   .'</td>'
					   .'<td>'
					   .'<input type="text" name="b_sjanger" class="inputBoks" onkeyup="validate(\'b_sjanger\');" onfocus="validate(\'b_sjanger\');" onblur="validate(\'b_sjanger\');" id="toval_b_sjanger" style="width:270px;" value="'.$BAND['b_sjanger'].'" />'
					   .validate('b_sjanger','threeletters')
					   .'</td>'
					   .'</tr>'
				   :'')
				   
	    # DISPLAY TECH DEMANDS IF IT IS A SCENE-BAND
			    . ($BAND['bt_id'] == 1 ? 
						'<tr>'
					   .'<td width="350" valign="top">'
					   .'<span class="font20">'. $lang['techdemands'] .'</span>'
					   .'<br /><span class="font11">'.$lang['techdemands-help'].'</span>'
					   .'</td>'
					   .'<td valign="top">'
					   .'<textarea name="td_demand" class="textarea" onkeyup="validate(\'td_demand\');" onfocus="validate(\'td_demand\');" onblur="validate(\'td_demand\');" id="toval_td_demand">'.$BAND['td_demand'].'</textarea>'
					   .validate('td_demand','sixletters')
					   .'</td>'
					   .'</tr>'
				   :'')

		# DESCRIPTION		
				   .'<tr>'
				   .'<td width="350" valign="top">'
				   .'<span class="font20">'. $lang['description'] .'</span>'
				   .'<br /><span class="font11">'.$lang['description-help'].'</span>'
				   .'</td>'
				   .'<td valign="top">'
				   .'<textarea name="td_konferansier" class="textarea" onkeyup="validate(\'td_konferansier\');" onfocus="validate(\'td_konferansier\');" onblur="validate(\'td_konferansier\');" id="toval_td_konferansier">'.$BAND['td_konferansier'].'</textarea>'
				   .validate('td_konferansier','twentyletters')
				   .'</td>'
				   .'</tr>'
		
		# SHOW ON UKM?
				   .'<tr>'
				   .'<td width="350" valign="top">'
				   .'</td>'
				   .'<td valign="top">'
				   .'<span class="font11">'
				   .$lang['showonukm']
				   .'<br />'
				   .'</span>'
				   .'<div align="right" style="margin-right: 45px;">'
				   .'<label><input type="radio" name="public" value="ja" '.(empty($BAND['b_description'])?'':' checked="checked"').'/> Ja</label>'
				   .'<label><input type="radio" name="public" value="nei" '.(empty($BAND['b_description'])?' checked="checked"':'').' /> Nei</label>'
				   .'</div>'
				   .'</td>'
				   .'</tr>'
				   
				   .'</table>'
				   .'</fieldset>';
				   
		##################################################################################
		## 							 PARTICIPANTS INFOS									##
		##################################################################################
		## COLLECT PARTICIPANTS
		$participants = new SQL("SELECT `smartukm_participant`.*, `smartukm_rel_b_p`.`instrument` FROM `smartukm_participant`
								 JOIN `smartukm_rel_b_p` ON (`smartukm_participant`.`p_id` = `smartukm_rel_b_p`.`p_id`)
								 WHERE `smartukm_rel_b_p`.`b_id` = '#b_id'
								 ORDER BY `b_p_id` ASC",
								 array('b_id'=>$BAND['b_id']));
		$participants = $participants->run();
		
		$CONTENT .= '<fieldset>'
				   .'<legend>'.$lang['participants'].'</legend>'
				   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">';
		
		## IF THERE ARE NO PARTICPANTS
		if(mysql_num_rows($participants) == 0) {
			$CONTENT .= '<tr><td colspan="7"><img src="http://pamelding.ukm.no/img/stop.png" width="16" style="float: right;" />'.$lang['no_participants'].'</td></tr>';	
		## IF THERE ARE PARTICPANTS
		} else {
			$CONTENT .= '<tr><td colspan="7" style="height: 5px;"></td></tr>';	
			while($p = mysql_fetch_assoc($participants)) {
				$CONTENT .= '<tr>'
						   .'<td width="50" valign="middle"><img src="/img/user_2.png" width="32"/></td>'
						   .'<td width="*%" valign="middle">'
						   .'<p style="margin-bottom: 3px;" class="font20">'.$p['p_firstname'].' '.$p['p_lastname'].'</p>'
						   .$p['instrument'] .' &nbsp; '
						   .'</td>'
						   ## ALERT IF MISSING - SHOULD NOT BE POSSIBLE (ONLY IN CASE OF ADMIN-MISTAKE)
						   .'<td width="80" align="center" valign="middle">'
								.(false ? '<img src="/img/alert.png" width="32"/>'
											. '<br>'
											. '<span class="font11">Info mangler</span>' : '')
						   .'</td>'
						   ## SPACER
						   .'<td width="45" align="center"></td>'
						   ## EDIT ICON
						   .'<td width="50" valign="middle" align="center">'
							   .'<a href="javascript:saveAndGo(\'profilside\', \'people_add\', \''.$p['p_id'].'\');" style="text-decoration: none;">'
								.'<img src="/img/edit.png" width="32" border="0"/>'
								.'<br />'
								.'<span style="font-size: 11px; font-weight: normal; color: #000;">Rediger</span>'
								.'</a>'
						   .'</td>'
						   ## DELETE ICON
						   .'<td width="50" valign="middle" align="center">'
								.'<a href="javascript:saveAndGo(\'profilside\', \'people_delete\', \''.$p['p_id'].'\');" style="text-decoration:none;">'
								.'<img src="/img/delete.png" width="32" border="0"/>'
								.'<br />'
								.'<span style="font-size: 11px; font-weight: normal; color: #000;">Slett</span>'
								.'</a>'
							.'</td>'
						   ## SPACER
						   .'<td width="40" valign="middle" align="center"></td>'
						   .'</tr>'
						   ## SEPARATOR LINE
						   .'<tr><td colspan="7" style="border-bottom: 1px solid #ccc; height: 2px;"></td></tr>';
			}
		}
		
		## BUTTON TO ADD PARTICIPANTS
			$CONTENT .= '<tr><td colspan="7"></td></tr>'
					   .'<tr>'
					   .'<td width="50"></td>'
					   .'<td colspan="6" valign="middle">'
					   .'<p class="dinsideLink" style="margin-bottom: 3px;" class="font20">'
					   .'<a href="javascript:saveAndGo(\'profilside\', \'people_add\', \'new\');" style="text-decoration: none;">'
					   .'<img src="/img/add.png" width="20" border="0" /> '
					   .$lang['add_participants']
					   .'</a>'
					   .'</p>'
					   .'</td>'
					   .'</tr>';
		
		$CONTENT .= '</table>'
				   .'</fieldset>';
				   
				   
				   
		##################################################################################
		## 								 TITLES INFOS									##
		##################################################################################
		## FIND QUERY FOR TITLES
		switch($BAND['bt_id']) {
			## SCENE-BANDS
			case 1:
				switch($_GET['type']) {
					case 'scene':
					case 'teater':
					case 'annet':
					case 'litteratur':
						require_once('include/profilepage_php/qry_title_scene.inc.php');
						break;
					case 'dans':
						require_once('include/profilepage_php/qry_title_dans.inc.php');
						break;
				}
				break;
			## VIDEO-BANDS
			case 2:
				require_once('include/profilepage_php/qry_title_video.inc.php');
				break;
			## EXHIBITION BANDS
			case 3:
				require_once('include/profilepage_php/qry_title_kunst.inc.php');
				break;
			## OTHER (READ FOOD)-BANDS
			case 6:
				require_once('include/profilepage_php/qry_title_matkultur.inc.php');
				break;
		}
		## RUN QUERY	
		$titles = $titles->run();
		
		$CONTENT .= '<fieldset>'
				   .'<legend>'.$lang['titles'].'</legend>'
				   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">';
		
		## IF THERE ARE NO TITLES
		if(mysql_num_rows($titles) == 0) {
			$CONTENT .= '<tr><td colspan="7"><img src="http://pamelding.ukm.no/img/stop.png" width="16" style="float: right;" />'.$lang['no_titles'].'</td></tr>';	
		## IF THERE ARE TITLES
		} else {
			$CONTENT .= '<tr><td colspan="7" style="height: 5px;"></td></tr>';	
			while($t = mysql_fetch_assoc($titles)) {
				$CONTENT .= '<tr>'
						   .'<td width="50" valign="middle"><img src="/img/title_'.$_GET['type'].'.png" width="32"/></td>'
						   .'<td width="*%" valign="middle">'
						   .'<p style="margin-bottom: 3px;" class="font20">'.$t['title'].'</p>'
						   .(is_numeric($t['info']) ? timeFormat($t['info']) : $t['info']).' &nbsp; '
						   .'</td>'
						   ## ALERT IF MISSING
						   .'<td width="80" align="center" valign="middle">'
								.(false ? '<img src="/img/alert.png" width="32"/>'
											. '<br>'
											. '<span class="font11">Info mangler</span>' : '')
						   .'</td>'
						   ## SPACER
						   .'<td width="45" align="center"></td>'
						   ## EDIT ICON
						   .'<td width="50" valign="middle" align="center">'
							   .'<a href="javascript:saveAndGo(\'profilside\', \'title_add\', \''.$t['id'].'\');" style="text-decoration: none;">'
								.'<img src="/img/edit.png" width="32" border="0" />'
								.'<br />'
								.'<span style="font-size: 11px; font-weight: normal; color: #000;">Rediger</span>'
								.'</a>'
						   .'</td>'
						   ## DELETE ICON
						   .'<td width="50" valign="middle" align="center">'
								.'<a href="javascript:saveAndGo(\'profilside\', \'title_'.$_GET['type'].'_delete\', \''.$t['id'].'\');" style="text-decoration:none;">'
								.'<img src="/img/delete.png" width="32" border="0"/>'
								.'<br />'
								.'<span style="font-size: 11px; font-weight: normal; color: #000;">Slett</span>'
								.'</a>'
						   .'</td>'
						   ## SPACER
						   .'<td width="40" valign="middle" align="center"></td>'
						   .'</tr>'
						   ## SEPARATOR LINE
						   .'<tr><td colspan="7" style="border-bottom: 1px solid #ccc; height: 2px;"></td></tr>';
			}
		}
		
		## BUTTON TO ADD TITLES
			$CONTENT .= '<tr><td colspan="7"></td></tr>'
					   .'<tr>'
					   .'<td width="50" ></td>'
					   .'<td colspan="6" valign="middle">'
					   .'<p class="dinsideLink" style="margin-bottom: 3px; font-size: 20px;"><img src="/img/add.png" width="25" /> '
					   .'<a href="javascript:saveAndGo(\'profilside\', \'title_add\', \'new\');" style="text-decoration: none;">'
					   .$lang['add_title']
					   .'</a>'
					   .'</p>'
					   .'</td>'
					   .'</tr>';
		
		$CONTENT .= '</table>'
				   .'</fieldset>';
				   
		
		##################################################################################
		## 								 SAVE-BUTTON									##
		##################################################################################
		$CONTENT .= '<div style="margin: 20px;" align="center" width="520">'
				   .'<input type="submit" name="submitProfilepage" value="'.$lang['submit'].'" style="height:40px; width: 250px; font-size: 16px;" />'
				   .'<input type="button" name="submitAndLeave" value="'.$lang['submit_n_go'].'" onclick="saveAndGo(\'profilside\', \'goToYourPage\');" style="height:40px; width: 250px; font-size: 16px; margin-left: 20px;" />'
				   .'</div>'
				   .'<input type="hidden" name="theGo_input" id="theGo_input" />'
				   .'<input type="hidden" name="someID_input" id="someID_input" />'
				   .'</form>';
	}
}