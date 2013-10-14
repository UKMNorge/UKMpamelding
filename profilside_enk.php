<?php
if(!isset($_SESSION['PLACE_ID']) && !isset($_SESSION['KOMMUNE_ID'])) {
	header("Location: http://pamelding.ukm.no/");
	exit();	
}
require_once('language/profilside_enk/language_'.$_GET['type'].'.php');
		   
## IF EDITING, COLLECT USER INFOS
if(isset($_GET['id']) && $_GET['id']=='edit') {
	$qry = new SQL("SELECT * FROM `smartukm_participant`
				   JOIN `smartukm_rel_b_p` ON (`smartukm_rel_b_p`.`p_id` = `smartukm_participant`.`p_id`)
				   JOIN `smartukm_band` ON (`smartukm_band`.`b_id` = `smartukm_rel_b_p`.`b_id`)
				   WHERE `smartukm_band`.`b_id` = '#bid'
				   LIMIT 1",
				   array('bid'=>$_SESSION['B_ID']));
	$data = $qry->run('array');
	
	## CALCULATE AGE
	if($data['p_dob'] == 0) 
		$data['p_age'] = 16;
	else {
		$yob = date("Y", $data['p_dob']);
		$age = (int) date("Y") - (int) $yob;
		$data['p_age'] = $age;
	}
	## INITIATE CONTENT
	$CONTENT = '<h1>Rediger '.$data['p_firstname'].'</h1>';
} else {
	## INITIATE CONTENT
	$CONTENT = '<h1 style="margin: 2px;">'
			   .$lang['profilside_for'] . " "
			   .(empty($BAND['b_name']) ? $lang['ditt_nye_innslag']: $BAND['b_name'])
		   .'</h1>'
		  . $lang['melderdegnapa_2'] . $PLACE_NAME .'.<br />'
		  .'<br /><br />';
	# SET DATA ARRAY
	$data = array('p_firstname'=>'', 'p_lastname'=>'', 'p_age'=>0, 'p_email'=>'', 'p_phone'=>'', 'p_adress'=>'', 'p_postnumber'=>'','instrument'=>'','b_description'=>'');
}

## FIND AGE VALUES
$age = '<select name="p_age" onchange="validate(\'p_age\');" onfocus="validate(\'p_age\');" onblur="validate(\'p_age\');" onkeyup="validate(\'p_age\');" id="toval_p_age">'
	  .'<option value="0" '.($_GET['type'] !== 'edit' ? 'selected="selected"' :'') .' disabled="disabled">Velg alder</option>';
for($i=10; $i<26; $i++)	
$age .= '<option value="'.$i.'" '.($data['p_age']==$i ? 'selected="selected"':'').'>'.$i.'</option>';

$age .= '<option value="0" '.($data['p_age']==0 && $_GET['type'] == 'edit' ? 'selected="selected"':'').'>Over 25</option>'
	   .'</select>';

## FIND ACTION
 # if editing or already signed in, take user to dinside for save
 # if creating a new band from scratch, take user to SMS-page
 $editing = (isset($_GET['id']) && $_GET['id']=='edit') || isset($_SESSION['UKM_DINSIDE_UID']);
 $action = ( $editing ? findLink('dinside', $_GET['type']) : findLink('sms', $_GET['type']));
## SET THE FORM
$CONTENT .= '<form method="POST" action="'.$action.'" enctype="application/x-www-form-urlencoded">'
			# IF EDITING, GIVE PERSON AND BAND INFOS FOR SAVING
		   .(isset($_GET['id']) ? '<input type="hidden" value="'.$_SESSION['B_ID'].'" name="band_id" />'
								 .'<input type="hidden" value="'.$data['p_id'].'" name="person_id" />'
								: '')
		   ## IF NOT EDITING, BUT SIGNED IN, GIVE THE CONTACT (SIGNED IN) USER ID FOR RELATION PURPOSE
		   .(isset($_SESSION['UKM_DINSIDE_UID'])&&!isset($_GET['id']) ? '<input type="hidden" value="'.$_SESSION['UKM_DINSIDE_UID'].'" name="contact_id" />' : '')
		   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
		   		   
			# FIRST NAME
		   .'<tr>'
		   .'<td width="280">'
		   .'<span class="font20">'. $lang['fornavn'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_firstname" class="inputBoks" onkeyup="validate(\'p_firstname\');" onfocus="validate(\'p_firstname\');" onblur="validate(\'p_firstname\');" id="toval_p_firstname" value="'.$data['p_firstname'].'" />'
		   .validate('p_firstname','threeletters')
		   .'</td>'
		   .'</tr>'
		   
		   # SURNAME
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['etternavn'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_lastname" class="inputBoks" onkeyup="validate(\'p_lastname\');" onfocus="validate(\'p_lastname\');" onblur="validate(\'p_lastname\');" id="toval_p_lastname" value="'.$data['p_lastname'].'" />'
		   .validate('p_lastname','twoletters')
		   .'</td>'
		   .'</tr>'
		   
		   # AGE
		   .'<tr id="p_age_row">'
		   .'<td>'
		   .'<span class="font20">'. $lang['alder'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .$age
		   .validate('p_age','selectedsomething')		   
		   .'</td>'
		   .'</tr>'
		   
		   # SPACER
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'
		   
		   # EMAIL
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['e-post'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_email" class="inputBoks" onkeyup="validate(\'p_email\');" onfocus="validate(\'p_email\');" onblur="validate(\'p_email\');" id="toval_p_email" value="'.$data['p_email'].'" />'
		   .validate('p_email','email')
		   # IF NOT EDITING, SHOW HELP-TEXT FOR EMAIL
		   .(!$editing ? '<br /><span class="font12">'.$lang['obs-epost'].'</span>' : '')
		   .'</td>'
		   .'</tr>'
		   
		   # SPACER
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'
		   
		   # CELL PHONE
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['mobilnummer'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_phone_first" class="inputBoks" onkeyup="validate(\'p_phone_first\');" onfocus="validate(\'p_phone_first\');" onblur="validate(\'p_phone_first\');" id="toval_p_phone_first" value="'.$data['p_phone'].'" />'
		   .validate('p_phone_first','cellphone')
		   .'</td>'
		   .'</tr>'
		   
		   ## IF NOT EDITING, SHOW THE REPEAT CELL AND HELP-TEXT
		   .(!$editing ? '<tr>'
			   .'<td>'
			   .'<span class="font20">'. $lang['gjentamobil'] .'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="p_phone_second" class="inputBoks" onkeyup="validate(\'p_phone_second\');" onfocus="validate(\'p_phone_second\');" onblur="validate(\'p_phone_second\');" id="toval_p_phone_second" value="'.$data['p_phone'].'" />'
			   .validate('p_phone_second','secondcellphone')
	
			   .'<br /><span class="font12">'.$lang['obs-mobil'].'</span>'
			   .'</td>'
			   .'</tr>'
			  :'')
		   
		   # SPACER
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'
		   
		   # ADDRESS
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['adresse'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_address" class="inputBoks" onkeyup="validate(\'p_address\');" onfocus="validate(\'p_address\');" onblur="validate(\'p_address\');" id="toval_p_address" value="'.$data['p_adress'].'" />'
		   .validate('p_address','threeletters')
		   .'</td>'
		   .'</tr>'		   

			# POSTNUMBER
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['postnummer'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_postalcode" size="4" class="inputBoks"
			 onblur="validate(\'p_postalcode\');"
			 id="toval_p_postalcode"  value="'.$data['p_postnumber'].'" />'
			 # IF EDITING, GET POSTALPLACE FROM DATABASE
		   .' <span id="p_postalplace" style="font-size:13px;"> '.(isset($_GET['id']) && $_GET['id'] == 'edit'?$data['p_postplace']:$lang['poststedaut']).'</span>'
		   .validate('p_postalcode','postalcode')
		   .'</td>'
		   .'</tr>';
		   
			# CALCULATE POSSIBLE FUNCTIONS FOR GIVEN BAND TYPE
	$CONTENT_ADD = '';
	switch($_GET['type']) {
		# CASE OF A NETTREDAKSJONSBAND
		case 'nettredaksjon':
			$checks = array('journalist','fotograf','videoreporter','flerkamera,regi','flerkamera,kamera');
			for($i=0; $i<sizeof($checks); $i++) {
				$CONTENT_ADD .= '<input type="checkbox" name="function[]" '
					   		.(strpos(strtolower($data['instrument']), $checks[$i])!==false ? 'checked="checked"':'') 
							.' value="'.ucfirst($checks[$i]).'" />'.ucfirst($checks[$i]).'<br />';
			}
			break;
		# CASE OF A SCENETEKNIKK-BAND
		case 'sceneteknikk':
			$checks = array('lyd','lys','scenecrew');
			for($i=0; $i<sizeof($checks); $i++) {
				$CONTENT_ADD .= '<input type="checkbox" name="function[]" '
					   		.(strpos(strtolower($data['instrument']), $checks[$i])!==false ? 'checked="checked"':'') 
							.' value="'.ucfirst($checks[$i]).'" />'.ucfirst($checks[$i]).'<br />';
			}
			break;
	}
	   # IF THE BAND TYPE HAS PREDEFINED FUNCTIONS, IT IS TIME TO SHOW THEM
	if(!empty($CONTENT_ADD))
		$CONTENT .= 
   		   '<tr><td colspan="2"> &nbsp; </td></tr>'
		   .'<tr id="p_age_row">'
		   .'<td valign="top">'
		   .'<span class="font20">'. $lang['funksjon'] .'</span>'
		   .'</td>'
		   .'<td>'
		   . $CONTENT_ADD
		   .'</td>'
		   .'</tr>';
	## IF NOT, PRINT AN EMPTY ARRAY
	else {
		$CONTENT .= 
   		   '<tr><td colspan="2"> &nbsp; </td></tr>'
		   .'<tr id="p_age_row">'
		   .'<td valign="top">'
		   .'<span class="font20"></span>'
		   .'</td>'
		   .'<td><input type="hidden" value="'.ucfirst($_GET['type']).'" name="function[]" /></td>'
		   .'</tr>';
	}

	$CONTENT .= '<tr>'
			   .'<td valign="top">'
			   .'<span class="font20">'. $lang['description'] .'</span>'
			   .'<br /><span class="font12">'.$lang['description-help'].'</span>'
			   .'</td>'
			   .'<td valign="top">'
			   .'<textarea name="b_description" class="textarea" onkeyup="validate(\'b_description\');" onfocus="validate(\'b_description\');" onblur="validate(\'b_description\');" id="toval_b_description">'.$data['b_description'].'</textarea>'
			  # .validate('b_description','tenletters')
			   .'</td>'
			   .'</tr>';

	## SUBMITBUTTON
	## CHANGING NAME AND VALUE DEPENDING ON EDIT/CREATE
	$CONTENT .= 
		   '<tr>'
		   .'<td colspan="2">'
		   .'<input type="submit" name="submitSingle'.(isset($_GET['id'])&&$_GET['id'] == 'edit'? 'Upd':'').'" value="'.((isset($_GET['id']) && $_GET['id'] == 'edit') || isset($_SESSION['UKM_DINSIDE_UID'])? $lang['save']:$lang['submit']).'" onclick="return validateForm();" />'
		   .'</td>'
		   .'</tr>'

		   .'</table>'
		   .'</form>';
?>