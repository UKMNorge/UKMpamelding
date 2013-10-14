<?php
$titles = new SQL("SELECT 
				  `t_id` AS `id`,
				  `t_e_title` AS `title`,
				  `t_e_type` AS `info`,
				  `t_e_technique` AS `additional`
				  FROM `smartukm_titles_exhibition`
				  WHERE `b_id` = '#b_id'
				  ORDER BY `t_id` ASC",
				  array('b_id'=>$BAND['b_id']));
?>