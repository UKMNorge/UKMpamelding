<?php
$titles = new SQL("SELECT 
				  `t_id` AS `id`,
				  `t_name` AS `title`,
				  `t_time` AS `info`,
				  `t_coreography` AS `additional`
				  FROM `smartukm_titles_scene`
				  WHERE `b_id` = '#b_id'
				  ORDER BY `t_id` ASC",
				  array('b_id'=>$BAND['b_id']));
?>