<?php
## ARRAY OF INFOS TO BE SAVED
	$title_infos = array(
						'b_id'=>$_SESSION['B_ID'],
						't_title'=>$_POST['t_title'],
						't_format'=>$_POST['t_format'],
						't_time'=>$_POST['t_time'],
						'season'=>$SEASON);
## QUERY IF INSERT
	$insert = "INSERT INTO `smartukm_titles_video` 
			(`t_id` ,`b_id` ,`t_v_made_by` ,`t_v_title` ,`t_v_time` ,`t_v_format` ,`t_v_comments` ,`season`)
			VALUES
			('' , '#b_id', '', '#t_title', '#t_time', '#t_format', '', '#season');";
## QUERY IF UPDATE
	$update = "UPDATE `smartukm_titles_video`
			  SET `t_v_title` = '#t_title',
				  `t_v_time` = '#t_time',
				  `t_v_format`='#t_format'
			  WHERE `t_id` = '#t_id'";
			  
require_once('title_save.inc.php');
?>