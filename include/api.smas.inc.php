<?php
## SMART-MEDIA API-CALL  - USED BY UKM TO LOG SMS INTO PAYMENT TABLE ##
class APIcall {
	var $result = array();
	public function APIcall($file, $params) {
		global $ss3;
		# API-URL
		$url = 'http://api.ss3.no/'.str_replace('.php','',$file).'.php';
		# ADD THE SYSTEM IDS
		$url .= '?system=ukmno&id='.md5(base64_decode('dWttbm9BUElTUzNwZXBwZXI2MmFHcktlWWVaVDQ='));
		# LOOP ALL PARAMS
		foreach($params as $key => $val) $url .= '&'.$key.'='.$val;
		
		# PERFORM THE CURL REQUEST
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		# IF IT GETS A 404 - SOME INFOS ARE MISSING - FAIL
		if(strpos($result, '<head><title>404 Not Found</title></head>') !== false)
			$this->result = array(false, '404');
		
		# EXPLODE TO GET FEEDBACK
		$result = explode('|', $result);
		
		# GET THE REAL RESULT (array[0])
		$bool = $result[0];
		
		# GET THE FEEDBACKS
		$feedback = explode('=>', $result[1]);
		
		$this->result = array($bool, $feedback);
		#return $this->result;
	}
	
	public function run() {
		return $this->result;
	}
}