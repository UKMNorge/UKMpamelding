<?php
	# REDIRECT TO PERSON-PAGE
	header("Location: " . findLink('person',$_GET['type'],$_POST['someID_input']));
	exit();
?>