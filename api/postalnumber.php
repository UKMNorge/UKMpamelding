<?php
ini_set('session.cookie_domain', 'pamelding.ukm.no');
ini_set('session.gc_maxlifetime', 60*60*8);
session_save_path('/tmp/pamelding.ukm.no');
ini_set('session.gc_probability', 1);
session_start();
require_once('../include/database.inc.php');
$query = new SQL("SELECT `postalplace` FROM `smartcore_postalplace` WHERE `postalcode` = '#code'",
				 array('code'=>(int) $_GET['postalcode']));
$place = $query->run('field','postalplace');
if(empty($place)) echo 'false';
else echo utf8_encode($place);
