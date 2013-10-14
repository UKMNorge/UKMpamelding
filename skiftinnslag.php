<?php
$qry = new SQL("SELECT `smartukm_rel_pl_b`.`pl_id`, `smartukm_band`.`b_kommune`, `smartukm_band`.`bt_id`, `smartukm_band`.`b_kategori`
			   FROM `smartukm_rel_pl_b`
			   JOIN `smartukm_band` ON (`smartukm_band`.`b_id` = `smartukm_rel_pl_b`.`b_id`)
			   WHERE `smartukm_band`.`b_id` = '#bid'
			   AND `smartukm_rel_pl_b`.`season` = '#season'",
			   array('bid'=>$_GET['type'], 'season'=>$SEASON));
$b = $qry->run('array');

$_SESSION['B_ID'] = $_GET['type'];
$_SESSION['PLACE_ID'] = $b['pl_id'];
$_SESSION['KOMMUNE_ID'] = $b['b_kommune'];

if($b['bt_id'] == '1') {
	$type = ($b['b_kategori'] == 'musikk') ? 'scene' : $b['b_kategori'];
	if(strpos($type,'annet p')!== false)
		$type = 'annet';
	$type = str_replace(array('på scenen','annet pÃ¥ scenen'),'',$type);
} else {
	switch($b['bt_id']) {
		case '2':		$type = 'video';			break;
		case '3':		$type = 'utstilling';		break;
		case '6':		$type = 'matkultur';		break;
		case '4':		$type = 'konferansier';		break;
		case '5':		$type = 'nettredaksjon';	break;
		case '8':		$type = 'arrangor';			break;
		case '9':		$type = 'sceneteknikk';		break;
	}
}

## IF WE DID NOT RECOGNIZE THE BAND TYPE
if(!isset($type)||$type == '') {
	header("Location: ". findLink('dinside', 'feilSkift'));
	exit();
## IF THE BAND TYPE IS A WORK-BAND
} elseif(in_array($type, $WORK)) {
	header("Location: ". findLink('profilside_enk', $type, 'edit'));
	exit();
## IF IT IS A STANDARD-BAND
} else {
	header("Location: ". findLink('profilside_std', $type));
	exit();
}
?>