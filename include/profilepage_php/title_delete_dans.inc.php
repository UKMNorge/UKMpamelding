<?php
## SET THE QUERY
$delete = "DELETE FROM `smartukm_titles_scene`
			 WHERE `t_id` = '#t_id'
			 AND `b_id` = '#b_id'
			 AND `season` = '#season'";
## REQUIRE THE ACTION-FILE
require_once('title_delete.inc.php');
?>