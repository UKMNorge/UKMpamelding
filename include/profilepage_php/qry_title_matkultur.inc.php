<?php
$titles = new SQL("SELECT 
				  `t_id` AS `id`,
				  `t_o_function` AS `title`,
				  `t_o_comments` AS `info`
				  FROM `smartukm_titles_other`
				  WHERE `b_id` = '#b_id'
				  ORDER BY `t_id` ASC",
				  array('b_id'=>$BAND['b_id']));
?>