<?php
require_once('language/sms/language.php');

##################################################################################
## 				IF CHANGED PHONE NUMBER FOR CONTACT PERSON						##
##################################################################################
if(isset($_POST['submitNewPhone']))
	require_once('include/sms/newphone.inc.php');
##################################################################################
## 				IF ISSET THE CONTACT PERSON SUBMIT-BUTTON						##
##################################################################################
if(isset($_POST['submitContact']) || isset($_POST['submitSingle']) && !isset($_SESSION['B_ID']))
	require_once('include/sms/create.inc.php');
#### ALL STUFF CREATED

## SET HEADER
if(isset($DIE)) {
	$CONTENT = $DIE;
} else {
	$CONTENT = '<h1 style="color: #000;">'.$lang['entercode'].'</h1>'
			. (isset($_GET['wrong']) ? '<span style="color: #f52626; font-weight:bold;">'.
						   ($_GET['wrong'] == 'still' ? $lang['SMS-againstill'] : $lang['SMS-again']).'</span>' :
			   str_replace('#PHONE', '<span style="color:#0000ff;">'. $_POST['p_phone_first'].'</span>', $lang['tekst']))
			.' <br /><br /><br />';
	
	## PARTICIPANT PHONE IS SET (USER COMES DIRECTLY FROM CONTACT P PAGE OR NEWPHONE PAGE)
	if(isset($_POST['p_phone_first']))
		$phone = $_POST['p_phone_first'];
	## PARTICIPANT PHONE IS NOT SET (USER CLICKED REFRESH)
	else {
		$phone = new SQL("SELECT `p_phone` FROM `smartukm_participant`
						 JOIN `smartukm_band` ON (`smartukm_band`.`b_contact` = `smartukm_participant`.`p_id`)
						 WHERE `smartukm_band`.`b_id` = '#b_id'",
						 array('b_id'=>$_SESSION['B_ID']));
		$phone = $phone->run('field','p_phone');
	}
	
	## IF IT IS A SINGLE-PERSON BAND, USER SHOULD NEVER GO TO PROFILE PAGE
	$action = (in_array($_GET['type'], $WORK) ? findLink('sms_enk') : findLink('profilside_std', $_GET['type']));
	
	
	## PRINT SMS-FORM
	## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	## POSSIBLE BUG-SOURCE STILL ACTIVE!
	#  IF USER IS OUTSIDE SERVER TIMEZONE, WAITTIME IS CALCULATED IN PHP - SHOULD BE TOTALLY DONE IN JAVASCRIPT
	## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	$CONTENT .= '<form method="POST" action="'.$action.'" enctype="application/x-www-form-urlencoded">'
			   .'<table cellpadding="0" cellspacing="0" border="0" align="center">'
			   .'<tr>'
			   .'<td width="100">'
			   .'<span style="font-size:20px;">'. $lang['SMS-kode'] .'</span> '
			   .'</td>'
			   .'<td align="left" style="padding-left: 2px;">'
			   .' <input type="text" name="SMScode" class="inputBoks" id="smscode" />'
			   .'</td>'
			   .'</tr>'
			   .'<tr>'
			   .'<td colspan="2" style="padding-top:40px;">'
			   .'<span class="font12">'
			   . '<span  style="font-weight: bold;">'.$lang['ikkemottatt'].'</span>'
			   .'<br />'
			   . str_replace(array('#LINK','#PHONE'), array(findLink('newphone',$_GET['type']), $phone), $lang['sjekk1'])
			   .'<br />'
			   . str_replace(array('#LINK','#WAITTIME'), array('javascript:waitAndGo('. (time()+(60*4)) .');','<span id="WAITTIME"></span>'), $lang['sjekk2'])
			   .'</span>'
			   .'</td>'
			   .'</tr>'
			   .'<tr>'
			   .'<td colspan="2" style="padding-top:20px;">'
			   .'<input type="submit" name="submit" value="'.$lang['submit'].'" onclick="return checkCodeEntered();" />'
			   .'</td>'
			   .'</tr>'
			   .'</table>'
			   .'</form>'
			   
			   .'<script language="javascript" type="text/javascript">'
			   .'calcWT();'
			   .'</script>';
}