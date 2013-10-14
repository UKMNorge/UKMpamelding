<?php
session_start();
error_reporting(E_ALL);
ini_set('session.gc_maxlifetime', 60*60*8);
if(!isset($_GET['steg']))
	$_GET['steg'] = 'start';
	
if($_SERVER['REMOTE_ADDR'] == '84.49.37.206')  {
	var_dump($_SESSION);
	var_dump($_SERVER['PHPSESSID']);
	var_dump($_SERVER);
} else {
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
			session_regenerate_id();
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

if($_SERVER['REMOTE_ADDR'] != '84.49.37.206')  {
	
	// Secure input from file-attack
	foreach ($_GET as $key => $val)
		$_GET[$key] = secure($val);
	foreach ($_POST as $key => $val)
		if(!is_array($_POST[$key])) 
			$_POST[$key] = secure($val);
}
require_once('include/database.inc.php');
require_once('include/config.inc.php');
require_once('include/toolkit.inc.php');

if($_GET['steg'] == 'velg_type' && isset($_GET['type']) && isset($_GET['id'])) {
	$_SESSION['PLACE_ID'] = $PLACE_ID = $_GET['type'];
	$_SESSION['KOMMUNE_ID'] = $KOMMUNE_ID = $_GET['id'];
} elseif(isset($_SESSION['KOMMUNE_ID']) && isset($_SESSION['PLACE_ID'])) {
	$PLACE_ID = $_SESSION['PLACE_ID'];
	$KOMMUNE_ID = $_SESSION['KOMMUNE_ID'];
}
if(!in_array($_GET['steg'], $FILES))
	$_GET['steg'] = 'start';

												  
## INKLUDER VENSTREKOLONNE
require_once('include/place.inc.php');

require_once($_GET['steg'].'.php');

## INKLUDER FOOTER
#require_once('include/header.inc.php');

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
    <td colspan="3" class="centralbanner"></td>
  </tr>
  <tr>
    <td class="leftborder"></td>
    <td class="centralcontent"><div>
        <!-- MENU AND CONTENT OF LEFT -->
        <div id="columnleft">'.$LEFT.'
        </div>
        <!-- END MENU AND CONTENT OF LEFT -->
        <!-- CENTRAL CONTENT -->
        <div id="columncenter">
          <div id="mycontent">'.$CONTENT.'</div>
        </div>
        <!-- END CENTRAL CONTENT -->
      </div></td>
    <td class="leftborder"></td>
  </tr>
  <tr>
    <td colspan="3" class="centralbottom" valign="bottom"><div id="copyright2009"> <a href="?side=168" style="color:#fff; font-size:10px;"> Kontakt oss: 46 41 55 00 | Arrang&oslash;rsupport : 46 42 16 25</a><br />
        <a style="color:#fff; font-size:10px;" href="http://www.ukm.no">Utvikling: UKM Norge</a> | <a href="http://uredd.no" style="color:#fff; font-size:10px;">Design: UREDD</a><br />
        <a style="color:#fff; font-size:10px;" xmlns:cc="http://creativecommons.org/ns#" href="http://www.ukm.no" property="cc:attributionName" rel="cc:attributionURL">UKM.no</a><a style="color:#fff; font-size:10px;" rel="license" target="_blank" href="http://creativecommons.org/licenses/by/3.0/no/"> er lisensiert under en Creative Commons Navngivelse 3.0 Norge lisens</a>. </div></td>
  </tr>
</table>';