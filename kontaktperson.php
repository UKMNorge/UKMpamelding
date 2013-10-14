<?php
if(!isset($_SESSION['PLACE_ID']) && !isset($_SESSION['KOMMUNE_ID'])) {
	header("Location: http://pamelding.ukm.no/");
	exit();	
}

require_once('language/kontaktperson/language_'.$_GET['type'].'.php');

## IF EDITING THE CONTACT PERSON
# DATA IS SET IN REQUIRING FILE
if($_GET['type'] == 'edit')
	$CONTENT = '<h1 style="color: #000;">Rediger kontaktperson</h1>';
else {
	## SET HEADER FOR A NEW CONTACT PERSON
	$CONTENT = '<h1>'.$lang['blikontakt'].'</h1>'
        . $lang['gibeskjed'] .' <br /><br /><br />';
	$data = array('p_firstname'=>'', 'p_lastname'=>'', 'p_age'=>0, 'p_email'=>'', 'p_phone'=>'', 'p_adress'=>'', 'p_postnumber'=>'');
}

## FIND AGE VALUES
$age = '<select name="p_age" onchange="validate(\'p_age\');" onfocus="validate(\'p_age\');" onblur="validate(\'p_age\');" onkeyup="validate(\'p_age\');" id="toval_p_age">'
	  .'<option value="0" '.($_GET['type'] !== 'edit' ? 'selected="selected"' :'') .' disabled="disabled">Velg alder</option>';
for($i=10; $i<26; $i++)	
$age .= '<option value="'.$i.'" '.($data['p_age']==$i ? 'selected="selected"':'').'>'.$i.'</option>';

$age .= '<option value="0" '.($data['p_age']==0 && $_GET['type'] == 'edit' ? 'selected="selected"':'').'>Over 25</option>'
	   .'</select>';

## SET THE FORM
$CONTENT .= '<form method="POST" action="'.($_GET['type'] !== 'edit' ? findLink('sms', $_GET['type']) : findLink('dinside')).'" enctype="application/x-www-form-urlencoded">'
		   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'

## IF NOT EDITING, ASK IF CONTACT P PARTICIPATES IN BAND
		   .($_GET['type'] !== 'edit' ? 
			    '<tr>'
			   .'<td>'
			   .'<span class="font20">'. $lang['deltattiproduksjonen'] .'</span><br />'
			   .'<span class="font12">'.$lang['deltattiproduksjonen_hjelp'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<label><input type="radio" name="participates" value="ja" selected="selected" onchange="fixFunction(1);" onfocus="fixFunction(1);" onblur="fixFunction(1);" /> Ja</label>'
			   .'<label><input type="radio" name="participates" value="nei" onchange="fixFunction(0);" onfocus="fixFunction(0);" onblur="fixFunction(0);" /> Nei</label>'
			   .'</td>'
			   .'</tr>'
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
			   : ''
			)

## FIRST NAME
		   .'<tr>'
		   .'<td width="280">'
		   .'<span class="font20">'. $lang['fornavn'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_firstname" class="inputBoks" onkeyup="validate(\'p_firstname\');" onfocus="validate(\'p_firstname\');" onblur="validate(\'p_firstname\');" id="toval_p_firstname" value="'.$data['p_firstname'].'" />'
		   .validate('p_firstname','twoletters')
		   .'</td>'
		   .'</tr>'
		   
## SURNAME
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['etternavn'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_lastname" class="inputBoks" onkeyup="validate(\'p_lastname\');" onfocus="validate(\'p_lastname\');" onblur="validate(\'p_lastname\');" id="toval_p_lastname" value="'.$data['p_lastname'].'" />'
		   .validate('p_lastname','twoletters')
		   .'</td>'
		   .'</tr>'

## AGE
		   .'<tr id="p_age_row">'
		   .'<td>'
		   .'<span class="font20">'. $lang['alder'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .$age
		   .validate('p_age','selectedsomething')		   
		   .'</td>'
		   .'</tr>'

## SPACER
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'

## EMAIL
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['e-post'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_email" class="inputBoks" onkeyup="validate(\'p_email\');" onfocus="validate(\'p_email\');" onblur="validate(\'p_email\');" id="toval_p_email" value="'.$data['p_email'].'" />'
		   .validate('p_email','email')
		   ## IF EDITING CONTACT P, DONT SHOW MAIL HELP-TEXT
		   .($_GET['type'] !== 'edit' ? '<br /><span class="font12">'.$lang['obs-epost'].'</span>' : '')
		   .'</td>'
		   .'</tr>'

## SPACER
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'

## CELL PHONE NUMBER
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['mobilnummer'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_phone_first" class="inputBoks" onkeyup="validate(\'p_phone_first\');" onfocus="validate(\'p_phone_first\');" onblur="validate(\'p_phone_first\');" id="toval_p_phone_first" value="'.$data['p_phone'].'" />'
		   .validate('p_phone_first','cellphone')
		   .'</td>'
		   .'</tr>'

## REPEAT CELL PHONE
# HIDDEN IF EDITING THE CONTACT P
		   .($_GET['type'] !== 'edit' ?
			'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['gjentamobil'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_phone_second" class="inputBoks" onkeyup="validate(\'p_phone_second\');" onfocus="validate(\'p_phone_second\');" onblur="validate(\'p_phone_second\');" id="toval_p_phone_second" value="'.$data['p_phone'].'" />'
		   .validate('p_phone_second','secondcellphone')

		   . '<br /><span class="font12">'.$lang['obs-mobil'].'</span>'
		   .'</td>'
		   .'</tr>'
		    : '')
		   
## SPACER		   
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'

## ADDRESS
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['adresse'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_address" class="inputBoks" onkeyup="validate(\'p_address\');" onfocus="validate(\'p_address\');" onblur="validate(\'p_address\');" id="toval_p_address" value="'.$data['p_adress'].'" />'
		   .validate('p_address','threeletters')
		   .'<br /><span class="font12">'.$lang['noadress_whattowrite'].'</span>'
		   .'</td>'
		   .'</tr>'		   

## POST NUMBER

		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['postnummer'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_postalcode" size="4" class="inputBoks"
			 onblur="validate(\'p_postalcode\');"
			 id="toval_p_postalcode"  value="'.$data['p_postnumber'].'" />'
		   ## IF EDITING, WE KNOW THE PLACE FROM THE DATABASE
		   .' <span id="p_postalplace" style="font-size:13px;"> '.($_GET['type']!=='edit'?$lang['poststedaut']:$data['p_postplace']).'</span>'
		   .validate('p_postalcode','postalcode')
		   .'</td>'
		   .'</tr>'	

## SPACER
   		   .'<tr><td colspan="2"> &nbsp; </td></tr>'

## IF NOT EDITING, ASK FOR FUNCTION
		   .($_GET['type'] !== 'edit' ?
			    '<tr id="row_function">'
			   .'<td>'
			   .'<span class="font20">'. $lang['dinfunksjon'] .'</span><br />'
			   .'<span class="font12">'.$lang['dinfunksjon_hjelp'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="p_function" class="inputBoks" onkeyup="validate(\'p_function\');" onfocus="validate(\'p_function\');" onblur="validate(\'p_function\');" id="toval_p_function" />'
			   .validate('p_function','threeletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
				: '')

		   .'<tr><td colspan="2" style="font-weight: bold; color: #f3776f;">'
		   .'Jeg godkjenner at min opptreden p&aring; UKM kan bli fotografert/filmet, <br />'
		   .'og at bildet/opptaket kan bli brukt av UKM p&aring; nett eller i publikasjoner<br />'
		   .'</td></tr>'
		   .'<tr><td colspan="2"> &nbsp; </td></tr>'
## IF EDITING, SHOW SAVE-TEXT, IF NOT SHOW SMS-TEXT
   		   .'<tr>'
		   .'<td colspan="2">'
		   .'<input type="submit" name="submitContact" value="'.($_GET['type'] !== 'edit' ? $lang['submit'] : $lang['save']).'" onclick="return validateForm();" />'
		   .'</td>'
		   .'</tr>'

		   .'</table>'
		   .'</form>';
?>