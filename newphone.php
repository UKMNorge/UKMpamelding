<?php

## USED IF THE USER WANTS TO CHANGE CELLPHONE
# SIMPLE FORM, NO FUN
require_once('language/newphone/language.php');

$CONTENT = '<h1>'.$lang['riktignummer'].'</h1>'
		   .'<form method="POST" action="'.findLink('sms', $_GET['type']).'" enctype="application/x-www-form-urlencoded">'
		   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'

		   .'<tr>'
		   .'<td width="280">'
		   .'<span class="font20">'. $lang['mobilnummer'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_phone_first" class="inputBoks" onkeyup="validate(\'p_phone_first\');" onfocus="validate(\'p_phone_first\');" onblur="validate(\'p_phone_first\');" id="toval_p_phone_first" />'
		   .validate('p_phone_first','cellphone')
		   .'</td>'
		   .'</tr>'
		   
		   .'<tr>'
		   .'<td>'
		   .'<span class="font20">'. $lang['gjentamobil'] .'</span>'
		   .'</td>'
		   .'<td>'
		   .'<input type="text" name="p_phone_second" class="inputBoks" onkeyup="validate(\'p_phone_second\');" onfocus="validate(\'p_phone_second\');" onblur="validate(\'p_phone_second\');" id="toval_p_phone_second" />'
		   .validate('p_phone_second','secondcellphone')

		   .'<br /><span style="font-size:12px;">'.$lang['obs-mobil'].'</span>'
		   .'</td>'
		   .'</tr>'
		   
		   .'<tr><td colspan="2">&nbsp;  </td></tr>'
		   
		   .'<tr>'
		   .'<td colspan="2">'
		   .'<input type="submit" name="submitNewPhone" value="'.$lang['submit'].'" onclick="return validateForm();" />'
		   .'</td>'
		   .'</tr>'
		   
		   .'</table>'
		   .'</form>'
		   ;