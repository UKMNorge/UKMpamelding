<?php
ini_set('session.cookie_domain', 'pamelding.ukm.no');
ini_set('session.gc_maxlifetime', 60*60*8);
session_start();
error_reporting(E_ALL);

if(isset($_GET['plid'])&&isset($_GET['kommune'])) {
	$_SESSION['PLACE_ID'] = $_GET['plid'];
	$PLACE_ID = $_GET['plid'];
	$_SESSION['KOMMUNE_ID'] = $_GET['kommune'];
	$KOMMUNE_ID = $_GET['kommune'];

	header("Location: http://pamelding.ukm.no/?steg=".$_GET['steg']."&type=".$_GET['type']);
	exit();
}
header("Location: http://pamelding.ukm.no/");
exit();

?>