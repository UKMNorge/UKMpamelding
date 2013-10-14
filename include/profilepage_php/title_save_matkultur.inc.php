<?php
## ARRAY OF INFOS TO BE SAVED
	$title_infos = array(
						'b_id'=>$_SESSION['B_ID'],
						't_o_function'=>$_POST['t_o_function'],
						't_o_comments'=>$_POST['t_o_comments'],
						'season'=>$SEASON);
## QUERY IF INSERT
	$insert = "INSERT INTO `smartukm_titles_other` 
			(`t_id` ,`b_id` ,`t_o_function` ,`t_o_experience` ,`t_o_comments`, `season`)
			VALUES
			('' , '#b_id', '#t_o_function', '', '#t_o_comments', '#season');";
## QUERY IF UPDATE
	$update = "UPDATE `smartukm_titles_other`
			  SET `t_o_function` = '#t_o_function',
				  `t_o_comments`='#t_o_comments'
 		  WHERE `t_id` = '#t_id'";
			  
require_once('title_save.inc.php');
?>