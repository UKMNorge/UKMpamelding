<?php
$_GET['id'] = $_GET['type'];
$_GET['type'] = 'edit';

$qry = new SQL("SELECT * FROM `smartukm_participant`
			   WHERE `p_id` = '#pid'",
			   array('pid'=>$_SESSION['UKM_DINSIDE_UID']));
$data = $qry->run('array');

if($data['p_dob'] == 0) 
	$data['p_age'] = 16;
else {
	$yob = date("Y", $data['p_dob']);
	$age = (int) date("Y") - (int) $yob;
	$data['p_age'] = $age;
	#$data['p_age'] = $age-10;
}
require_once('kontaktperson.php');
$CONTENT .= '<br />'
		   .'<a href="'.findLink('dinside').'" style="font-weight:normal;">'
		   .$lang['avbryt']
		   .'</a>';
?>