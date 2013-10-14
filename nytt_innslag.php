<?php
$p = new SQL("SELECT * FROM `smartukm_participant`
			 WHERE `p_id` = '#pid'",
			 array('pid'=>$_SESSION['UKM_DINSIDE_UID']));
$p = $p->run('array');

## LET THE USER SELECT WHAT KIND OF BAND TO SUBSCRIBE
if(!isset($_GET['type'])) {
	$NYTT_INNSLAG = true;
	require_once('velg_type.php');
## CREATE THE BAND THE USER WANTS
} else {
	###########################################################
	## CALCULATE INFOS NOT GIVEN
		$bt_id = getBTIDfromImage($_GET['type']);
		$kategori = ($_GET['type'] == 'scene') ? 'musikk' : (($_GET['type'] == 'annet') ? 'annet på scenen' : $_GET['type']);
		
		if(!isset($PLACE_ID) || empty($PLACE_ID))
			$CONTENT = 'Mangler mønstring!';
		else {
	## CREATE INFO ARRAY
		$band_infos = array('b_contact'=>$_SESSION['UKM_DINSIDE_UID'], # LOGGED ON USER
							'season'=>$SEASON, # FROM DATABASE
							'bt_id'=>$bt_id, # FROM GET TYPE IN URL
							'time'=>time(),
							'kategori'=>($bt_id==1 ? $kategori : ''), # IF SCENE (BT_ID==1), GIVE CATEGORY
							'kommune'=>$p['p_kommune']);
	# QUERY TO INSERT
		$band = "INSERT INTO `smartukm_band` 
				(`b_id` ,`b_name` ,`b_contact` ,`b_year` ,`b_description` ,`b_password` ,`bt_id` ,`b_status` ,`b_subscr_time` ,
				 `b_kategori` ,`b_sjanger` ,`b_kommune` ,`b_nb_part` ,`b_status_text` ,`b_validatedby`)
				VALUES
				('' , '', '#b_contact', '#season', '', '', '#bt_id', '1', '#time', '#kategori', '', '#kommune', '0', '', '');";

		$band = "INSERT INTO `smartukm_band` 
				(`b_id`,`b_contact`,`b_year`,`bt_id`,`b_status`,`b_subscr_time`,`b_kategori`,`b_kommune`,`b_season`)
				VALUES
				('' , '#b_contact', '#season', '#bt_id', '1', '#time', '#kategori', '#kommune','#season');";

	
	## CREATE SQL AND INSERT
		$b_object = new SQL($band, $band_infos);	  
		$b_object = $b_object->run();
		$B_ID = mysql_insert_id();
		//stat_realtime_add($KOMMUNE_ID, $B_ID, $bt_id, ($bt_id==1?$kategori:''), 0, $SEASON);
		require_once('UKM/curl.class.php');
		$CURL = new UKMCURL();
		$CURL->request('http://api.ukm.no/innslag:statistikk_oppdater/'. $B_ID);


	
		###########################################################
		##				TECHNICAL DEMANDS OBJECT				 ##
		###########################################################
		$tech = "INSERT INTO `smartukm_technical` 
				(`td_id` ,`pl_id` ,`b_id` ,`td_demand` ,`td_konferansier`)
				VALUES
				('' , '#pl_id', '#b_id', '', '');";
		$tech = new SQL($tech, 
						array('pl_id'=>$PLACE_ID,
							  'b_id'=>$B_ID));
		$tech->run();
		
		###########################################################
		##					 RELATION TO PLACE					 ##
		###########################################################
		$pl_b = "INSERT INTO `smartukm_rel_pl_b`
				(`pl_id` ,`b_id` ,`season`)
				VALUES
				('#pl_id', '#b_id', '#season');";
		$pl_b = new SQL($pl_b, 
						array('pl_id'=>$PLACE_ID,
							  'b_id'=>$B_ID,
							  'season'=>$SEASON));
		$pl_b->run();
		
		# LOG BAND CREATED
			logIt($B_ID, 25, $PLACE_ID);
			$_SESSION['B_ID'] = $B_ID;
			header("Location: ".findLink('profilside_std', $_GET['type']));
			exit();
		}
}
?>