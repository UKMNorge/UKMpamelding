<?php
require_once('language/person/language_'.$_GET['type'].'.php');

if($_GET['id'] == 'new') {
	$p = array('p_firstname'=>'','p_lastname'=>'','p_dob'=>'','instrument'=>'','age'=>0,'p_phone'=>'');
	
	$inBand = new SQl("SELECT * FROM `smartukm_rel_b_p` WHERE `p_id` = '#uid' AND `b_id` = '#bid'",
					array('uid'=>$_SESSION['UKM_DINSIDE_UID'], 'bid'=>$_SESSION['B_ID']));
	$inBand = $inBand->run();
	$inBand = mysql_num_rows($inBand) == 1 ? true : false;
} else {
	$inBand = true;
	$p = new SQL("SELECT `p_firstname`,`p_lastname`,`p_dob`,`p_phone`,`instrument`
				 FROM `smartukm_participant`
			     JOIN `smartukm_rel_b_p` ON (`smartukm_participant`.`p_id` = `smartukm_rel_b_p`.`p_id`)
			     WHERE `smartukm_rel_b_p`.`season` = '#season'
			     AND `smartukm_rel_b_p`.`b_id` = '#b_id'
			     AND `smartukm_participant`.`p_id` = '#p_id'",
					   array('season'=>$SEASON, 'b_id'=>$_SESSION['B_ID'], 'p_id'=>$_GET['id']));
	$p = $p->run('array');
	$yob = date("Y", $p['p_dob']);
	$age = date("Y") - $yob;
	$p['age'] = $age;
}
## SET HEADER
$CONTENT = '<h1>'.($_GET['id'] == 'new' ? $lang['add'] : $lang['edit'].' '.$p['p_firstname'].' '.$p['p_lastname']).'</h1>';

## FIND AGE VALUES
$age = '<select name="p_age" onchange="validate(\'p_age\');" onfocus="validate(\'p_age\');" onblur="validate(\'p_age\');" onkeyup="validate(\'p_age\');" id="toval_p_age">'
	  .'<option value="0" selected="selected" disabled="disabled">Velg alder</option>';
for($i=10; $i<26; $i++)	$age .= '<option value="'.$i.'" '. ($i==$p['age'] ? ' selected="selected" ':'') .'>'.$i.'</option>';
$age .= '<option value="0">Over 25</option>'
	   .'</select>';
	   
## SET THE FORM
if(!$inBand) 
	$CONTENT .= '<fieldset>'
			   .'<legend>Skal du legge til deg selv i innslaget?</legend>'
			   .'<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" id="contactCPform" enctype="application/x-www-form-urlencoded">'
			   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'

			   .'<tr id="row_function">'
			   .'<td widt="280">'
			   .'<span class="font20">'. $lang['dinfunksjon'] .'</span><br />'
			   .'<span class="font12">'.$lang['dinfunksjon_hjelp'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="p_my_function" value="'.$p['instrument'].'" class="inputBoks" onkeyup="validate(\'p_my_function\');" onfocus="validate(\'p_my_function\');" onblur="validate(\'p_my_function\');" id="toval_p_my_function" />'
			   #.validate('p_function','threeletters')
			   .'</td>'
			   .'</tr>'
		   
			   .'<tr>'
			   .'<td>'
			   .'<input type="button" name="addMeToMyBand" onclick="validateTheFunctionOfCP();" value="'.$lang['addMe'].'" />'
			   .'</td>'
			   .'</tr>'
			   .'</table>'
			   .'</form>'
			   .'</fieldset>'
			   .'<fieldset>'
			   .'<legend>Eller skal du legge til en annen person  i innslaget?</legend>';

$CONTENT .= '<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" enctype="application/x-www-form-urlencoded">'
		   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
	   
		   .'<tr>'
		   .'<td width="280">'
		   .'<span class="font20">'. $lang['fornavn'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_firstname" value="'.$p['p_firstname'].'" class="inputBoks" onkeyup="validate(\'p_firstname\');" onfocus="validate(\'p_firstname\');" onblur="validate(\'p_firstname\');" id="toval_p_firstname" />'
		   .validate('p_firstname','twoletters')
		   .'</td>'
		   .'</tr>'
		   
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['etternavn'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_lastname" value="'.$p['p_lastname'].'" class="inputBoks" onkeyup="validate(\'p_lastname\');" onfocus="validate(\'p_lastname\');" onblur="validate(\'p_lastname\');" id="toval_p_lastname" />'
		   .validate('p_lastname','twoletters')
		   .'</td>'
		   .'</tr>'
		   
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['alder'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .$age
		   .validate('p_age','selectedsomething')		   
		   .'</td>'
		   .'</tr>'
		   
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['mobilnummer'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_phone" value="'.$p['p_phone'].'" class="inputBoks" onkeyup="validate(\'p_phone\');" onfocus="validate(\'p_phone\');" onblur="validate(\'p_phone\');" id="toval_p_phone" />'
		   .validate('p_phone','cellphone')
		   .'</td>'
		   .'</tr>'
		   
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'
		   
		   .'<tr id="row_function">'
		   .'<td>'
		   .'<span class="font20">'. $lang['dinfunksjon'] .'</span><br />'
		   .'<span class="font12">'.$lang['dinfunksjon_hjelp'].'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_function" value="'.$p['instrument'].'" class="inputBoks" onkeyup="validate(\'p_function\');" onfocus="validate(\'p_function\');" onblur="validate(\'p_function\');" id="toval_p_function" />'
		   .validate('p_function','threeletters')
		   .'</td>'
		   .'</tr>'
		   
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'

   		   .'<tr>'
		   .'<td colspan="2">'
		   .'<input type="hidden" name="p_id" value="'.$_GET['id'].'" />'
		   .'<input type="submit" name="people_save" value="'.$lang['submit'].'" onclick="return validateFormPS();" />'
		   .'</td>'
		   .'</tr>'
		   .'</table>'
		   .'</form>';
if(!$inBand) 
	$CONTENT .= '</fieldset>';

$CONTENT .= '<br />'
		   .'<a href="'.findLink('profilside_std',$_GET['type']).'" style="font-weight:normal;">'
		   .$lang['avbryt']
		   .'</a>';
?>