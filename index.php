<?php
ini_set('session.cookie_domain', 'pamelding.ukm.no');
ini_set('session.gc_maxlifetime', 60*60*8);
session_start();
error_reporting(E_ALL);

if(!isset($_SESSION['UKM_PAM_BROWSER'])) {
	require_once('include/browser.inc.php');
	$browser = new Browser();
	$_SESSION['UKM_PAM_BROWSER'] = $browser->getBrowser();
}

if(!isset($_GET['steg']))
	$_GET['steg'] = 'start';
	
if($_SESSION['UKM_PAM_BROWSER']!='Internet Explorer') {
	// This will make cookies/session available through PHP only (not JS, or similar)
	$params = session_get_cookie_params();
	session_set_cookie_params($params['lifetime'], $params['path'], $params['domain'], $params['secure'], true);	

	// Improve security by making sure the user has the same webrowser all the time
	// This is done to increase security against session-hijacking
	if (isset($_SESSION['HTTP_USER_AGENT'])) {
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'].'ukm')) {
			$_GET['logout'] = true;
		}
	}
	else {
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT'].'ukm');
		// Increase security
		#	session_regenerate_id();
	}
}
if(isset($_GET['logout'])) {
	unset($_SESSION['PLACE_ID']);
	unset($_SESSION['KOMMUNE_ID']);
	unset($_SESSION['SMSpass']);
	unset($_SESSION['B_ID']);
	unset($_SESSION['UKM_DINSIDE_UID']);
	unset($_SESSION['UKM_DINSIDE_PAS']);
	unset($_SESSION['HTTP_USER_AGENT']);
	header("Location: http://www.ukm.no");
	exit();
}

// Secure input from file-attack
function secure($input) {
	if (!is_array($input)) {
		return str_replace(array('../', './', '..\\', '.\\'), '', (urldecode($input)));
	}
	else {
		$output = null;		
		foreach ($input as $key => $value) {
			if (is_array($value)) {
				$output = secure($value);
			}	
			else {
				$output[$key] = secure($value);
			}
		}
	}
	return $output;
}

	// Secure input from file-attack
	foreach ($_GET as $key => $val)
		$_GET[$key] = secure($val);
	foreach ($_POST as $key => $val)
		if(!is_array($_POST[$key])) 
			$_POST[$key] = secure($val);

require_once('include/database.inc.php');
require_once('include/config.inc.php');
require_once('include/toolkit.inc.php');

if($_GET['steg'] == 'velg_type' && isset($_GET['type']) && isset($_GET['id'])) {
	## CHANGED 24.01.2011 : from x = y = z til x = z; y = z;
	$_SESSION['PLACE_ID'] = $_GET['type'];
	$PLACE_ID = $_GET['type'];
	$_SESSION['KOMMUNE_ID'] = $_GET['id'];
	$KOMMUNE_ID = $_GET['id'];
} elseif(isset($_SESSION['KOMMUNE_ID']) && isset($_SESSION['PLACE_ID'])) {
	$PLACE_ID = $_SESSION['PLACE_ID'];
	$KOMMUNE_ID = $_SESSION['KOMMUNE_ID'];
} 
## 24.01.2011
## POSSIBLE BUG IF IT HITS AN ELSE HERE?

if(!in_array($_GET['steg'], $FILES))
	$_GET['steg'] = 'start';

												  
## INKLUDER VENSTREKOLONNE
require_once('include/place.inc.php');

require_once($_GET['steg'].'.php');

## INKLUDER FOOTER
require_once('include/design.inc.php');
if(isset($_GET['steg'])&&$_GET['steg']=='dinside')
	echo str_replace('_#CONTENT#_',
				'<!-- CENTRAL CONTENT -->
				<div id="columncenter" style="width: 98%">
				  <div id="mycontent" style="width: 98%">'.($CONTENT).'</div>
				<br /><br /><br />&nbsp;
				</div>
		   <!-- END CENTRAL CONTENT -->',
		   $DESIGN);

else
	echo str_replace('_#CONTENT#_',
			'<!-- MENU AND CONTENT OF LEFT -->
				<div id="columnleft">'.($LEFT).'</div>
				<!-- END MENU AND CONTENT OF LEFT --> 
				<!-- CENTRAL CONTENT -->
				<div id="columncenter">
				  <div id="mycontent">'.($CONTENT).'</div>
				<br /><br /><br />&nbsp;
				</div>
		   <!-- END CENTRAL CONTENT -->',
		   $DESIGN);
/*
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
	.'<html xmlns="http://www.w3.org/1999/xhtml">'
	.'<head>'
	.'<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'
	.'<title>UKM P&aring;melding</title>'
	.'<link rel="stylesheet" href="stil.css" type="text/css">'
	.'<link rel="SHORTCUT ICON" href="http://www.ukm.no/ukmno/personal_data/favicon.ico" />'
	.'<script language="javascript" src="javascript.js"></script> '
	.'<head>'
	.'<body onLoad="validateFormPL();">'
	.'<table cellpadding="0" cellspacing="0" border="0" class="centraltable" align="center">
	  <tr>
		<td class="centralbanner"></td>
	  </tr>
	  <tr>
		<td class="centralcontent"><div> 
			<!-- MENU AND CONTENT OF LEFT -->
			<div id="columnleft">'.$LEFT.'</div>
			<!-- END MENU AND CONTENT OF LEFT --> 
			<!-- CENTRAL CONTENT -->
			<div id="columncenter">
			  <div id="mycontent">'.$CONTENT.'</div>
			<br /><br /><br />&nbsp;
			</div>
			<!-- END CENTRAL CONTENT --> 
		  </div>
		  </td>
	  </tr>
	  <tr>
		<td class="centralbottom" valign="middle" align="center"><a style="color:#fff; font-size:10px;" href="http://www.ukm.no">Utvikling: UKM Norge</a> | <a href="http://uredd.no" style="color:#fff; font-size:10px;">Design: UREDD</a><br />
        <a style="color:#fff; font-size:10px;" xmlns:cc="http://creativecommons.org/ns#" href="http://www.ukm.no" property="cc:attributionName" rel="cc:attributionURL">UKM.no</a><a style="color:#fff; font-size:10px;" rel="license" target="_blank" href="http://creativecommons.org/licenses/by/3.0/no/"> er lisensiert under en Creative Commons Navngivelse 3.0 Norge lisens</a>.</td>
	  </tr>
	</table>';
	*/