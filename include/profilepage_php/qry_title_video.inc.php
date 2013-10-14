<?php
$titles = new SQL("SELECT 
				  `t_id` AS `id`,
				  `t_v_title` AS `title`,
				  `t_v_time` AS `info`,
				  `t_v_format` AS `additional`
				  FROM `smartukm_titles_video`
				  WHERE `b_id` = '#b_id'
				  ORDER BY `t_id` ASC",
				  array('b_id'=>$BAND['b_id']));
?>