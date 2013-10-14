<?php
/*

Class Name: pwdGen
Version: 1.2
Author(s): David Hurst
Copyright belongs to the author, but this code may be freely distributed provided this header remains in tact.

x - a lower case letter
X - an upper case letter
c - a lower case consonant
C - an upper case consonant
v - a lower case vowel
V - an upper case vowel
0 - a number from 0-9
* - a symbol
*/

class pwdGen {
	var $pattern;
	
	function pwdGen($pattern='Cvcv00X') {
		$this->pattern = $pattern;
	}
	
	function newPwd() {
		$new_pwd = "";
		$pttn = $this->pattern;
		for($i = 0; $i <= strlen($this->pattern); $i++) {
			$pattern_arr[$i] = substr($pttn, 0, 1);
			$pttn = substr($pttn, 1);
		}
		unset($pattern_arr[$i - 1]);
		foreach($pattern_arr as $value) {
			switch($value) {
				case "x":
				$new_pwd .= $this->randomLetter();
				break;
				case "X":
				$letter = $this->randomLetter();
				$new_pwd .= strtoupper($letter);
				break;
				case "v":
				$new_pwd .= $this->randomVowel();
				break;
				case "V":
				$letter = $this->randomVowel();
				$new_pwd .= strtoupper($letter);
				break;
				case "c":
				$new_pwd .= $this->randomCons();
				break;
				case "C":
				$letter = $this->randomCons();
				$new_pwd .= strtoupper($letter);
				break;
				case "0":
				$new_pwd .= $this->randomNumber();
				break;
				case "*":
				$new_pwd .= $this->randomSymbol();
				break;
			}
		}
	$this->pwd = $new_pwd;
	return $new_pwd;
	}
	
	function hash() {
		return md5('ukmno' . $this->pwd . 'padjfoaifiASDAosjdo3neslk5435wfnlADSFlascnjuon.kzdj342filjfsodsn');
	}
	
	function randomVowel() {
		$letter_arr = explode(',','a,e,u');
		$i = rand(0, count($letter_arr) - 1);
		return $letter_arr[$i];
	}
	
	function randomCons() {
		$letter_arr = explode(',','b,c,d,f,g,h,j,k,m,n,p,q,r,s,t,v,w,x,y,z');
		$i = rand(0, count($letter_arr) - 1);
		return $letter_arr[$i];
	}
	
	function randomLetter() {
		$letter_arr = explode(',','a,b,c,d,e,f,g,h,j,k,m,n,p,q,r,s,t,u,v,x,y,z');
		$i = rand(0, count($letter_arr) - 1);
		return $letter_arr[$i];
	}
	
	function randomNumber() {
		$i = rand(1, 9);
		return $i;
	}
	
	function randomSymbol() {
		$symbol_arr = explode(',','!,@,%,&,*,+,-,?,=');
		$i = rand(0, count($symbol_arr) - 1);
		return $symbol_arr[$i];
	}
}
?>