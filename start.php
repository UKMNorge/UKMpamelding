<?php
require_once('language/start/language.php');


$CONTENT ='<h1>'.$lang['logg_inn'].'</h1>'
		  .'<p style="margin-bottom: 0px;">' . $lang['description'] .' </p>';
		  
		  ## DIN SIDE LOG ON
$CONTENT .= '<form action="'.$_SERVER['PHP_SELF'].'?steg=dinside" method="POST">'
		  .'<strong>'.$lang['din_side'].'</strong>'
		  .'<div>'
		  	.$lang['e-post']. ' <input type="text" name="epost" id="dinsideEpost" style="height: 19px;" /> '
			.$lang['passord'].' <input type="password" name="passord" style="width: 100px; height: 19px;" /> '
		    .'<input type="submit" name="logon" value="'.$lang['logon'].'" />'
		    .'<br />'
		  .'<a href="javascript:forgottenDinSidePass(\'?steg=dinside&email=\');" style="font-weight:normal;">Glemt passord?</a>'
		  .'</div>';

$CONTENT .= '<br /><br /><h1>'.$lang['subscribe'].'</h1>'
		  . $lang['description_sub'] .' <br />';		  
## 

		  

## FIND ALL FYLKE AND LOOP
$fylker = new SQL("SELECT * FROM `smartukm_fylke` ORDER BY `name` ASC");
$fylker = $fylker->run();
while($f = mysql_fetch_assoc($fylker)) {
	if($f['id'] > 20)
		continue;
	$CONTENT .= '<div style="float: left; padding: 10px; width: 150px; margin-bottom: 20px;">'
			  . '<h3>'.$f['name'].'</h3>'
			  . '<a id="vis_'.$f['id'].'" href="javascript:show('.$f['id'].');">Vis lokalm&oslash;nstringer</a>'
			  . '<a style="display:none;" id="skjul_'.$f['id'].'" href="javascript:hide('.$f['id'].');">Skjul lokalm&oslash;nstringer</a>'
			  . '<div id="lokal_'.$f['id'].'" style="display: none;">';
	
	$kommuner = new SQL("SELECT `smartukm_kommune`.`name`, `smartukm_kommune`.`id`, `smartukm_place`.`pl_name`, `smartukm_place`.`pl_id`  
						FROM `smartukm_kommune`
						JOIN `smartukm_rel_pl_k` ON (`smartukm_kommune`.`id` = `smartukm_rel_pl_k`.`k_id`)
						JOIN `smartukm_place` ON (`smartukm_rel_pl_k`.`pl_id` = `smartukm_place`.`pl_id`)
						WHERE `smartukm_kommune`.`idfylke` = '#fylke'
						AND `smartukm_place`.`season` = '#season'
						AND `smartukm_kommune`.`id` NOT REGEXP '^.+90$'
						ORDER BY `smartukm_place`.`pl_name` ASC",
						array('fylke'=>$f['id'], 'season'=>$SEASON));
	$kommuner = $kommuner->run();
	while($k = mysql_fetch_assoc($kommuner))
		$CONTENT .= '<a href="'.findLink('velg_type', $k['pl_id'], $k['id']).'">'.$k['name'].'</a><br />';
	
	$CONTENT .= '</div></div>';
}

$CONTENT .= '<script type="text/javascript" language="javascript">'
."function show(id) {
document.getElementById('lokal_'+id).style.display = '';
document.getElementById('skjul_'+id).style.display = '';
document.getElementById('vis_'+id).style.display = 'none';

}"

."function hide(id) {
document.getElementById('lokal_'+id).style.display = 'none';
document.getElementById('skjul_'+id).style.display = 'none';
document.getElementById('vis_'+id).style.display = '';

}"

.'</script>';