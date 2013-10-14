<?php
## ARRAY OF INFOS TO BE SAVED
	$title_infos = array(
						'b_id'=>$_SESSION['B_ID'],
						't_name'=>$_POST['t_name'],
						't_titleby'=>$_POST['t_titleby'],
						't_music_by'=>$_POST['t_music_by'],
						't_time'=>$_POST['t_time'],
						'season'=>$SEASON);
## QUERY IF INSERT
	$insert = "INSERT INTO `smartukm_titles_scene` 
			(`t_id` ,`b_id` ,`t_name` ,`t_titleby` ,`t_musicby` ,`t_coreography` ,`t_time` ,`season`)
			VALUES
			('' , '#b_id', '#t_name', '#t_titleby', '#t_music_by', '', '#t_time', '#season');";
## QUERY IF UPDATE
	$update = "UPDATE `smartukm_titles_scene`
			  SET `t_name` = '#t_name',
				  `t_titleby` = '#t_titleby',
				  `t_musicby`='#t_music_by',
				  `t_time`='#t_time'
			  WHERE `t_id` = '#t_id'";
			  
require_once('title_save.inc.php');
?>