<?php
	## REDIRECT TO TITLE-PAGE 
	header("Location: " . findLink('title',$_GET['type'],$_POST['someID_input']));
	exit();
?>