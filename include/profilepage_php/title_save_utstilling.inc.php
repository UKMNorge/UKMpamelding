<?php
## ARRAY OF INFOS TO BE SAVED
		$title_infos = array(
							'b_id'=>$_SESSION['B_ID'],
							't_title'=>$_POST['t_title'],
							't_type'=>$_POST['t_type'],
							't_technique'=>$_POST['t_technique'],
							't_format'=>$_POST['t_format'],
							't_comments'=>$_POST['t_comments'],
							'season'=>$SEASON);
## QUERY IF INSERT
	$insert = "INSERT INTO `smartukm_titles_exhibition` 
					(`t_id` ,`b_id`, `t_e_made_by` ,`t_e_title` ,`t_e_type` ,`t_e_technique` ,`t_e_format` ,`t_e_comments` ,`season`)
					VALUES
					('' , '#b_id', '', '#t_title', '#t_type', '#t_technique', '#t_format', '#t_comments', '#season');";
## QUERY IF UPDATE
	$update = "UPDATE `smartukm_titles_exhibition`
					  SET `t_e_title` = '#t_title',
					  	  `t_e_type` = '#t_type',
						  `t_e_technique`='#t_technique',
						  `t_e_format`='#t_format',
						  `t_e_comments`='#t_comments'
					  WHERE `t_id` = '#t_id'";
			  
require_once('title_save.inc.php');
?>