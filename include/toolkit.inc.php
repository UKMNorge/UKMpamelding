<?php
function findLink($one, $two='', $three='') {
	global $URL;
	if(empty($three) && empty($two))
		return $URL.'?steg='.$one;
	elseif(empty($three))
		return $URL.'?steg='.$one.'&type='.$two;
	else 
		return $URL.'?steg='.$one.'&type='.$two.'&id='.$three;
}

function phone($phone) {
	return substr($phone,0,3) .' '.substr($phone,3,2).' '.substr($phone,5,3);	
}


function validate($name, $validateWhat) {
	return ' <img src="img/stop.png" width="16" alt="'.$validateWhat.'" id="validate_'.$name.'" />';
}

/*function timeFormat($seconds, $text=false) {
    $ret = "";
    if(intval(intval($seconds) / 3600) > 0)
        $ret .= intval(intval($seconds) / 3600) .($text ? 'time(r) ' : ':' );
 
    if(intval(intval($seconds) / 3600) > 0 || bcmod((intval($seconds) / 60),60) > 0)
        $ret .= bcmod((intval($seconds) / 60),60) .($text ? ' min ' : ':' );
		
    return $ret . (bcmod(intval($seconds),60)>0?bcmod(intval($seconds),60):'') .($text ? (bcmod(intval($seconds),60)==0 ? '' : ' sek ') : '' );
}*/
function timeFormat($sec, $text=false) {

    // start with a blank string
    $hms = "";
    
    $hours = intval(intval($sec) / 3600); 

    // add hours to $hms (with a leading 0 if asked for)
    if($hours > 0) 
   		$hms .= (!$text) 
        	  ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
	          : $hours. " time";
    
    // dividing the total seconds by 60 will give us the number of minutes
    // in total, but we're interested in *minutes past the hour* and to get
    // this, we have to divide by 60 again and then use the remainder
    $minutes = intval(($sec / 60) % 60); 

    // add minutes to $hms (with a leading 0 if needed)
    $hms .= (!$text)
			? str_pad($minutes, 2, "0", STR_PAD_LEFT). ":"
			: ($minutes == 0
			   ? ''
			   : $minutes. " min ");

    // seconds past the minute are found by dividing the total number of seconds
    // by 60 and using the remainder
    $seconds = intval($sec % 60); 

    // add seconds to $hms (with a leading 0 if needed)
    $hms .= (!$text)
			? str_pad($seconds, 2, "0", STR_PAD_LEFT)
			: ($seconds == 0
			   ? ''
			   : $seconds. " sek");

    // done!
    return $hms;
    
  }


function getBTIDfromImage($image) {
	switch($image) {
		case 'video': 			return 2;	
		case 'utstilling': 		return 3;	
		case 'scene': 			
		case 'dans': 			
		case 'teater': 			
		case 'litteratur': 			
		case 'annet': 			return 1;	
		case 'matkultur': 		return 6;	
		case 'konferansier':	return 4;
		case 'nettredaksjon': 	return 5;	
		case 'arrangor': 		return 8;	
		case 'sceneteknikk': 	return 9;	
	}
}


function logIt($b_id, $code, $freetext="") {
	$log = new SQL("INSERT INTO `ukmno_smartukm_log`
				   (`log_id` ,`log_time` ,`log_b_id` ,`log_code` ,`log_browser` ,`log_freetext`)
				   VALUES
				   ('' , '#time', '#b_id', '#code', '#browser', '#freetext');",
				   array('time'=>time(),
						 'b_id'=>$b_id,
						 'code'=>$code,
						 'browser'=>$_SERVER['HTTP_USER_AGENT'],
						 'freetext'=>$freetext));
	$log = $log->run();
}

function sendMail($to, $subject, $HTMLmessage, $TEXTmessage=false) {
	require_once('include/phpmailer/class.phpmailer.php');
	$mail = new PHPMailer(); // the true param means it will throw exceptions on errors, which we need to catch
	$mail->IsSMTP(); // telling the class to use SMTP
	
	try {
	  $mail->SMTPAuth   = true;                  // enable SMTP authentication
	  $mail->SetFrom('post@support.ukm.no', 'UKM Norge');
	  $mail->ClearReplyTos();
	  $mail->AddReplyTo('support@ukm.no', 'UKM Norge');
	  
	  $mail->AddAddress($to['address'], $to['name']);
	  $mail->Subject = $subject;

	  if($TEXTmessage)
	  	$mail->AltBody = $TEXTmessage;
	  
	  $mail->MsgHTML($HTMLmessage);

		$mail->Send();
	} catch (phpmailerException $e) {
	  $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  $e->getMessage(); //Boring error messages from anything else!
	}
#	if($_SERVER['REMOTE_ADDR']=='193.91.207.86')
#		var_dump($mail);
}