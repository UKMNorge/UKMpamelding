<?php
## URL TIL P&Aring;MELDINGSPROSESSEN
$URL = 'http://pamelding.ukm.no/';
## SMS-NUMMER TIL SUPPORTANSVARLIG
$SUPPORTSMS = 46415500;
$SUPPORTPHONE = 46421625;
$SUPPORTMAIL = 'support@ukm.no';

## FINN AKTIV SESONG
$season_q = new SQL("SELECT * FROM `smartcore_config` WHERE `conf_name` = 'smartukm_season'");
$SEASON = $season_q->run('field','conf_value');

## STEG-FILER SOM KAN INKLUDERES
$FILES = array('velg_type','kontaktperson','sms','newphone','dinside',
			   'support','profilside_std','person','faq','title','start',
			   'skiftinnslag','redigerkontakt','nytt_innslag','profilside_enk',
			   'sms_enk');

require_once('UKM/inc/pamelding-bandtypes.inc.php');

$WORK = array('nettredaksjon','konferansier','arrangor','sceneteknikk');

## ## ## ## ## ## ## ## ## ## ## ## ##
## VERKT&Oslash;Y

## VAR DUMP FORMATTERT
function pre_dump($val) {
	echo '<pre>';
	var_dump($val);
	echo '</pre>';
}
?>