<?php
$ERR = '<h1>En feil har oppst&aring;tt!</h1>'
	   .'Beklager, men en databasefeil har oppst&aring;tt. Vennligst <a href="mailto:support@ukm.no&subject=Feil med database">kontakt support</a>';

require_once('UKMconfig.inc.php');
$db = @mysql_connect(UKM_DB_HOST,UKM_DB_WRITE_USER,UKM_DB_PASSWORD) or die($ERR);
if (!$db) die($ERR);
mysql_select_db(UKM_DB_NAME);

class SQL {
	var $sql;
	
	function SQL($sql, $keyval=array()) {
		global $db;
		foreach($keyval as $key => $val) {
			if (get_magic_quotes_gpc())
				$val = stripslashes($val);
			if(!is_string($val)) {
				var_dump($val);
				debug_print_backtrace();
			}
			$sql = str_replace('#'.$key, mysql_real_escape_string(trim(strip_tags($val))), $sql);
		}
		$this->sql = $sql;
	}
	
	function run($what='resource', $name='') {
		global $db;
		$temp = mysql_query($this->sql);
		if(!$temp) return false;
		switch($what) {
			case 'field':
				$temp = mysql_fetch_array($temp);
				return $temp[$name];
			case 'array':
				return mysql_fetch_assoc($temp);
			default:
				return $temp;
		}
	}
	function debug() {
		return $this->sql;
	}
}

?>