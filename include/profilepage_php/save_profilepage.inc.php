<?php
logIt($_SESSION['B_ID'], 14, $_POST['b_name']);
## UPDATE THE BAND TABLE
$update = new SQL("UPDATE `smartukm_band`
				 SET `b_name` = '#b_name',
					 `b_sjanger` = '#b_sjanger',
					 `b_description` = '#description'
				 WHERE `b_id` = '#b_id'",
				 array('b_id'=>$_SESSION['B_ID'],
					   'b_name'=>$_POST['b_name'],
					   'b_sjanger'=>(isset($_POST['b_sjanger']) ? $_POST['b_sjanger'] : ''),	# SJANGER IS NOT INCLUDED IN EVERY BAND
					   'description'=>(isset($_POST['public'])&&$_POST['public']=='ja'?$_POST['td_konferansier']:'')));
$update = $update->run();

## UPDATE TECH DEMANDS AND PRESENTATIONS IF SET
$update = new SQL("UPDATE `smartukm_technical`
				  SET `td_konferansier` = '#description',
					  `td_demand` = '#demand'
				  WHERE `b_id` = '#b_id'",
					  array('description'=>$_POST['td_konferansier'],
							'b_id'=>$_SESSION['B_ID'],
							'demand'=>(isset($_POST['td_demand']) ? $_POST['td_demand'] : '')));
$update = $update->run();

# SINCE A ROW NOT NECESSEARILY NEED TO BE UPDATED FOR PROFILEPAGE TO BE SAVED, SAY ALWAYS TRUE
# (IN CASE OF NO CHANGES IN THE FIELDS ON SAVE...)
$MSG = array(true, $lang['profilepage_saved']);
$_SESSION['MSG'] = array(true, $lang['profilepage_saved']);

## VALIDATE THE BAND AGAIN
require_once('include/validation.inc.php');
validateBand($_SESSION['B_ID']);

if(isset($_POST['b_kommune']))
	logIt($_SESSION['B_ID'], 32, $_POST['b_kommune']);
?>