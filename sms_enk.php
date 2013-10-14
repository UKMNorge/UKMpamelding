<?php
require_once('language/profilepage_std/language.php');
### IF SMS-CODE IS WRONG, SEND BACK
if(isset($_POST['SMScode']) && $_POST['SMScode'] !== $_SESSION['SMSpass'])
	require_once('include/profilepage_php/sms_wrong.inc.php');
### IF SMS-CODE IS CORRECT, VALIDATE BAND
elseif(isset($_POST['SMScode']) && $_POST['SMScode'] === $_SESSION['SMSpass']) {
	$STATUS = 8;
	require_once('include/profilepage_php/sms_correct.inc.php');
	#pre_dump($_SESSION);
	header("Location: ".findLink('dinside'));
	$CONTENT = 'Beklager, en feil har oppstått, og vi jobber med å rette den. Vennligst prøv igjen litt senere';
}
?>