<?php
session_register();
if(md5($_GET['BID'].'yrdysafe') !== $_GET['sec'])
	die('Beklager, logg ikke tilgjengelig');
	
require_once('include/database.inc.php');

$b = new SQL("SELECT `b_name`, `b_status_text` FROM `smartukm_band` WHERE `b_id` = '#bid'",
			   array('bid'=>$_GET['BID']));
$b = $b->run('array');

$qry = new SQL("SELECT * FROM `ukmno log` WHERE `band` LIKE '#bid - %'",
			   array('bid'=>$_GET['BID']));
$qry = $qry->run();
echo '<table cellpadding="2" cellspacing="20" border="0">'
	.'<tr><td colspan="3"><h1>'.$b['b_name'].' - '.$_GET['BID'].'</h1><p>'.$b['b_status_text'].'</p></td></tr>'
	.'<tr>'
	.'<th>Tidspunkt</th>'
	.'<th>Logget</th>'
	.'<th>Beskrivelse</th>'
	.'</tr>';
while($r = mysql_fetch_assoc($qry)) {
	echo '<tr>';
	foreach($r as $row => $data)
		if($row != 'band')
			echo '<td>'. $data .'</td>';
	echo '</tr>';
}
echo '</table>';
?>