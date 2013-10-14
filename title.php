<?php
require_once('language/title/language_'.$_GET['type'].'.php');

##################################################################################
## 								VIDEO TITLES									##
##################################################################################
if($_GET['type'] == 'video') {
	if($_GET['id'] == 'new') {
		$t = array('t_v_title'=>'','t_v_format'=>'','t_v_time'=>'');
	} else {
		$t = new SQL("SELECT `t_v_title`,`t_v_format`,`t_v_time`
					 FROM `smartukm_titles_video`
					 WHERE `smartukm_titles_video`.`t_id` = '#t_id'",
						   array('t_id'=>$_GET['id']));
		$t = $t->run('array');
	}
	
	## FIND TIME VALUES
	$t_time = '<select name="t_time" onchange="validate(\'t_time\');" onfocus="validate(\'t_time\');" onblur="validate(\'t_time\');" onkeyup="validate(\'t_time\');" id="toval_t_time">'
		  .'<option value="0" selected="selected" disabled="disabled">Velg varighet</option>';
	for($i=10; $i<600; $i+=10)
		$t_time .= '<option value="'.$i.'" '. ($i==$t['t_v_time'] ? ' selected="selected" ':'') .'>'.timeFormat($i, true).'</option>';
	
	$t_time .=	'</select>';

	## SET HEADER
	$CONTENT = '<h1 style="color: #000;">'.($_GET['id'] == 'new' ? $lang['add'] : $lang['edit'] .' '. $t['t_v_title']).'</h1>';
	
	## SET THE FORM
	$CONTENT .= '<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" enctype="application/x-www-form-urlencoded">'
			   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
		   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['title'] .'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_title" value="'.$t['t_v_title'].'" class="inputBoks" onkeyup="validate(\'t_title\');" onfocus="validate(\'t_title\');" onblur="validate(\'t_title\');" id="toval_t_title" />'
			   .validate('t_title','threeletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['format'] .'</span>'
			   .'<br /><span class="font12">'.$lang['format-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_format" value="'.$t['t_v_format'].'" class="inputBoks" onkeyup="validate(\'t_v_format\');" onfocus="validate(\'t_v_format\');" onblur="validate(\'t_v_format\');" id="toval_t_v_format" />'
			   .validate('t_v_format','twoletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['time'] .'</span>'
			   .'<br /><span class="font12">'.$lang['time-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .$t_time
			   .validate('t_time','selectedsomething')		   
			   .'</td>'
			   .'</tr>'
	
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
	
			   .'<tr>'
			   .'<td colspan="2">'
			   .'<input type="hidden" name="t_id" value="'.$_GET['id'].'" />'
			   .'<input type="submit" name="title_save_video" value="'.$lang['submit'].'" onclick="return validateFormPS();" />'
			   .'</td>'
			   .'</tr>'
			   .'</table>'
			   .'</form>'
			   .'<br />'
			   .'<a href="'.findLink('profilside_std',$_GET['type']).'" style="font-weight:normal;">'
			   .$lang['avbryt']
			   .'</a>';
##################################################################################
## 								EXHIBITION TITLES								##
##################################################################################
} elseif($_GET['type'] == 'utstilling') {
	## Set variables to blank
	if($_GET['id'] == 'new') {
		$t = array('t_e_title'=>'','t_e_type'=>'','t_e_technique'=>'','t_e_format'=>'','t_e_comments'=>'');
	## If existing title, fetch values
	} else {
		$t = new SQL("SELECT `t_e_title`,`t_e_type`,`t_e_technique`,`t_e_format`, `t_e_comments`
					 FROM `smartukm_titles_exhibition`
					 WHERE `t_id` = '#t_id'",
						   array('t_id'=>$_GET['id']));
		$t = $t->run('array');
	}

	## SET HEADER
	$CONTENT = '<h1 style="color: #000;">'. ($_GET['id']=='new' ? $lang['add'] : $lang['edit'].' '.$t['t_e_title']).'</h1>';
	
	## SET THE FORM
	$CONTENT .= '<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" enctype="application/x-www-form-urlencoded">'
			   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
		   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['title'] .'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_title" value="'.$t['t_e_title'].'" class="inputBoks" onkeyup="validate(\'t_title\');" onfocus="validate(\'t_title\');" onblur="validate(\'t_title\');" id="toval_t_title" />'
			   .validate('t_title','threeletters')
			   .'</td>'
			   .'</tr>'
	   
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'

			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['type'] .'</span>'
			   .'<br /><span class="font12">'.$lang['type-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_type" value="'.$t['t_e_type'].'" class="inputBoks" onkeyup="validate(\'t_type\');" onfocus="validate(\'t_type\');" onblur="validate(\'t_type\');" id="toval_t_type" />'
			   .validate('t_type','twoletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['technique'] .'</span>'
			   .'<br /><span class="font12">'.$lang['technique-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_technique" value="'.$t['t_e_technique'].'" class="inputBoks" onkeyup="validate(\'t_technique\');" onfocus="validate(\'t_technique\');" onblur="validate(\'t_technique\');" id="toval_t_technique" />'
			   .validate('t_technique','twoletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['format'] .'</span>'
			   .'<br /><span class="font12">'.$lang['format-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_format" value="'.$t['t_e_format'].'" class="inputBoks" onkeyup="validate(\'t_format\');" onfocus="validate(\'t_format\');" onblur="validate(\'t_format\');" id="toval_t_format" />'
			   .validate('t_format','twoletters')
			   .'</td>'
			   .'</tr>'	   
	   
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['comments'] .'</span>'
			   .'<br /><span class="font12">'.$lang['comments-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<textarea name="t_comments" class="textarea" onkeyup="validate(\'t_comments\');" onfocus="validate(\'t_comments\');" onblur="validate(\'t_comments\');" id="toval_t_comments">'.$t['t_e_comments'].'</textarea>'
			   .validate('t_comments','tenletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
	
			   .'<tr>'
			   .'<td colspan="2">'
			   .'<input type="hidden" name="t_id" value="'.$_GET['id'].'" />'
			   .'<input type="submit" name="title_save_utstilling" value="'.$lang['submit'].'" onclick="return validateFormPS();" />'
			   .'</td>'
			   .'</tr>'
			   .'</table>'
			   .'</form>'
			   .'<br />'
			   .'<a href="'.findLink('profilside_std',$_GET['type']).'" style="font-weight:normal;">'
			   .$lang['avbryt']
			   .'</a>';
##################################################################################
## 									MUSIC TITLES								##
##################################################################################
} elseif($_GET['type'] == 'scene' || $_GET['type'] == 'teater' || $_GET['type'] == 'annet' || $_GET['type'] == 'litteratur') {
	## Set variables to blank
	if($_GET['id'] == 'new') {
		$t = array('t_name'=>'','t_titleby'=>'','t_musicby'=>'','t_time'=>0);
	## If existing title, fetch values
	} else {
		$t = new SQL("SELECT `t_name`,`t_titleby`,`t_musicby`,`t_time`
					 FROM `smartukm_titles_scene`
					 WHERE `t_id` = '#t_id'",
						   array('t_id'=>$_GET['id']));
		$t = $t->run('array');
	}

	## FIND TIME VALUES
	$t_time = '<select name="t_time" onchange="validate(\'t_time\');" onfocus="validate(\'t_time\');" onblur="validate(\'t_time\');" onkeyup="validate(\'t_time\');" id="toval_t_time">'
		  .'<option value="0" selected="selected" disabled="disabled">Velg varighet</option>';
	for($i=10; $i<600; $i+=10)
		$t_time .= '<option value="'.$i.'" '. ($i==$t['t_time'] ? ' selected="selected" ':'') .'>'.timeFormat($i, true).'</option>';
	
	$t_time .=	'</select>';

	## SET HEADER
	$CONTENT = '<h1 style="color: #000;">'. ($_GET['id']=='new' ? $lang['add'] : $lang['edit'].' '.$t['t_name']).'</h1>';
	
	## SET THE FORM
	$CONTENT .= '<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" enctype="application/x-www-form-urlencoded">'
			   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
		   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['title'] .'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_name" value="'.$t['t_name'].'" class="inputBoks" onkeyup="validate(\'t_name\');" onfocus="validate(\'t_name\');" onblur="validate(\'t_name\');" id="toval_t_name" />'
			   .validate('t_name','threeletters')
			   .'</td>'
			   .'</tr>'

			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['music_by'] .'</span>'
			   .'<br /><span class="font12">'.$lang['music_by-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_music_by" value="'.$t['t_musicby'].'" class="inputBoks" onkeyup="validate(\'t_music_by\');" onfocus="validate(\'t_music_by\');" onblur="validate(\'t_music_by\');" id="toval_t_music_by" />'
			   .($_GET['type'] == 'scene' ? validate('t_music_by','twoletters') : '')
			   .'</td>'
			   .'</tr>'


			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['title_by'] .'</span>'
			   .'<br /><span class="font12">'.$lang['title_by-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_titleby" value="'.$t['t_titleby'].'" class="inputBoks" onkeyup="validate(\'t_titleby\');" onfocus="validate(\'t_titleby\');" onblur="validate(\'t_titleby\');" id="toval_t_titleby" />'
			   #.validate('t_titleby','twoletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['time'] .'</span>'
			   .'<br /><span class="font12">'.$lang['time-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .$t_time
			   .validate('t_time','selectedsomething')		   
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
	
			   .'<tr>'
			   .'<td colspan="2">'
			   .'<input type="hidden" name="t_id" value="'.$_GET['id'].'" />'
			   .'<input type="submit" name="title_save_scene" value="'.$lang['submit'].'" onclick="return validateFormPS();" />'
			   .'</td>'
			   .'</tr>'
			   .'</table>'
			   .'</form>'
			   .'<br />'
			   .'<a href="'.findLink('profilside_std',$_GET['type']).'" style="font-weight:normal;">'
			   .$lang['avbryt']
			   .'</a>';
##################################################################################
## 									DANCE TITLES								##
##################################################################################
} elseif($_GET['type'] == 'dans') {
	## Set variables to blank
	if($_GET['id'] == 'new') {
		$t = array('t_name'=>'','t_coreography'=>'','t_time'=>0);
	## If existing title, fetch values
	} else {
		$t = new SQL("SELECT `t_name`,`t_coreography`,`t_time`
					 FROM `smartukm_titles_scene`
					 WHERE `t_id` = '#t_id'",
						   array('t_id'=>$_GET['id']));
		$t = $t->run('array');
	}

	## FIND TIME VALUES
	$t_time = '<select name="t_time" onchange="validate(\'t_time\');" onfocus="validate(\'t_time\');" onblur="validate(\'t_time\');" onkeyup="validate(\'t_time\');" id="toval_t_time">'
		  .'<option value="0" selected="selected" disabled="disabled">Velg varighet</option>';
	for($i=10; $i<600; $i+=10)
		$t_time .= '<option value="'.$i.'" '. ($i==$t['t_time'] ? ' selected="selected" ':'') .'>'.timeFormat($i, true).'</option>';
	
	$t_time .=	'</select>';

	## SET HEADER
	$CONTENT = '<h1 style="color: #000;">'. ($_GET['id']=='new' ? $lang['add'] : $lang['edit'].' '.$t['t_name']).'</h1>';
	
	## SET THE FORM
	$CONTENT .= '<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" enctype="application/x-www-form-urlencoded">'
			   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
		   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['title'] .'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_name" value="'.$t['t_name'].'" class="inputBoks" onkeyup="validate(\'t_name\');" onfocus="validate(\'t_name\');" onblur="validate(\'t_name\');" id="toval_t_name" />'
			   .validate('t_name','threeletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['coreography'] .'</span>'
			   .'<br /><span class="font12">'.$lang['coreography-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_coreography" value="'.$t['t_coreography'].'" class="inputBoks" onkeyup="validate(\'t_coreography\');" onfocus="validate(\'t_coreography\');" onblur="validate(\'t_coreography\');" id="toval_t_coreography" />'
			   .validate('t_coreography','twoletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['time'] .'</span>'
			   .'<br /><span class="font12">'.$lang['time-help'].'</span>'
			   .'</td>'
			   .'<td>'
			   .$t_time
			   .validate('t_time','selectedsomething')		   
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
	
			   .'<tr>'
			   .'<td colspan="2">'
			   .'<input type="hidden" name="t_id" value="'.$_GET['id'].'" />'
			   .'<input type="submit" name="title_save_dans" value="'.$lang['submit'].'" onclick="return validateFormPS();" />'
			   .'</td>'
			   .'</tr>'
			   .'</table>'
			   .'</form>'
			   .'<br />'
			   .'<a href="'.findLink('profilside_std',$_GET['type']).'" style="font-weight:normal;">'
			   .$lang['avbryt']
			   .'</a>';	
##################################################################################
## 									DANCE TITLES								##
##################################################################################
} elseif($_GET['type'] == 'matkultur') {
	## Set variables to blank
	if($_GET['id'] == 'new') {
		$t = array('t_o_function'=>'','t_o_comments'=>'');
	## If existing title, fetch values
	} else {
		$t = new SQL("SELECT `t_o_function`,`t_o_comments`
					 FROM `smartukm_titles_other`
					 WHERE `t_id` = '#t_id'",
						   array('t_id'=>$_GET['id']));
		$t = $t->run('array');
	}

	## SET HEADER
	$CONTENT = '<h1 style="color: #000;">'. ($_GET['id']=='new' ? $lang['add'] : $lang['edit'].' '.$t['t_o_function']).'</h1>';
	
	## SET THE FORM
	$CONTENT .= '<form method="POST" action="'.findLink('profilside_std', $_GET['type']).'" enctype="application/x-www-form-urlencoded">'
			   .'<table width="100%" cellpadding="2" cellspacing="2" border="0">'
		   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['title'] .'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_o_function" value="'.$t['t_o_function'].'" class="inputBoks" onkeyup="validate(\'t_o_function\');" onfocus="validate(\'t_o_function\');" onblur="validate(\'t_o_function\');" id="toval_t_o_function" />'
			   .validate('t_o_function','threeletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr>'
			   .'<td width="350">'
			   .'<span class="font20">'. $lang['description'] .'</span>'
			   .'</td>'
			   .'<td>'
			   .'<input type="text" name="t_o_comments" value="'.$t['t_o_comments'].'" class="inputBoks" onkeyup="validate(\'t_o_comments\');" onfocus="validate(\'t_o_comments\');" onblur="validate(\'t_o_comments\');" id="toval_t_o_comments" />'
			   .validate('t_o_comments','tenletters')
			   .'</td>'
			   .'</tr>'
			   
			   .'<tr><td colspan="2"> &nbsp; </td></tr>'
	
			   .'<tr>'
			   .'<td colspan="2">'
			   .'<input type="hidden" name="t_id" value="'.$_GET['id'].'" />'
			   .'<input type="submit" name="title_save_matkultur" value="'.$lang['submit'].'" onclick="return validateFormPS();" />'
			   .'</td>'
			   .'</tr>'
			   .'</table>'
			   .'</form>'
			   .'<br />'
			   .'<a href="'.findLink('profilside_std',$_GET['type']).'" style="font-weight:normal;">'
			   .$lang['avbryt']
			   .'</a>';	
}


?>