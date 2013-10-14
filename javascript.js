<!-- DATE FUNCTIONS -->
Date.prototype.format=function(format){var returnStr='';var replace=Date.replaceChars;for(var i=0;i<format.length;i++){var curChar=format.charAt(i);if(replace[curChar]){returnStr+=replace[curChar].call(this);}else{returnStr+=curChar;}}return returnStr;};Date.replaceChars={shortMonths:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],longMonths:['January','February','March','April','May','June','July','August','September','October','November','December'],shortDays:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],longDays:['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],d:function(){return(this.getDate()<10?'0':'')+this.getDate();},D:function(){return Date.replaceChars.shortDays[this.getDay()];},j:function(){return this.getDate();},l:function(){return Date.replaceChars.longDays[this.getDay()];},N:function(){return this.getDay()+1;},S:function(){return(this.getDate()%10==1&&this.getDate()!=11?'st':(this.getDate()%10==2&&this.getDate()!=12?'nd':(this.getDate()%10==3&&this.getDate()!=13?'rd':'th')));},w:function(){return this.getDay();},z:function(){return"Not Yet Supported";},W:function(){return"Not Yet Supported";},F:function(){return Date.replaceChars.longMonths[this.getMonth()];},m:function(){return(this.getMonth()<9?'0':'')+(this.getMonth()+1);},M:function(){return Date.replaceChars.shortMonths[this.getMonth()];},n:function(){return this.getMonth()+1;},t:function(){return"Not Yet Supported";},L:function(){return(((this.getFullYear()%4==0)&&(this.getFullYear()%100!=0))||(this.getFullYear()%400==0))?'1':'0';},o:function(){return"Not Supported";},Y:function(){return this.getFullYear();},y:function(){return(''+this.getFullYear()).substr(2);},a:function(){return this.getHours()<12?'am':'pm';},A:function(){return this.getHours()<12?'AM':'PM';},B:function(){return"Not Yet Supported";},g:function(){return this.getHours()%12||12;},G:function(){return this.getHours();},h:function(){return((this.getHours()%12||12)<10?'0':'')+(this.getHours()%12||12);},H:function(){return(this.getHours()<10?'0':'')+this.getHours();},i:function(){return(this.getMinutes()<10?'0':'')+this.getMinutes();},s:function(){return(this.getSeconds()<10?'0':'')+this.getSeconds();},e:function(){return"Not Yet Supported";},I:function(){return"Not Supported";},O:function(){return(-this.getTimezoneOffset()<0?'-':'+')+(Math.abs(this.getTimezoneOffset()/60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()/60))+'00';},P:function(){return(-this.getTimezoneOffset()<0?'-':'+')+(Math.abs(this.getTimezoneOffset()/60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()/60))+':'+(Math.abs(this.getTimezoneOffset()%60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()%60));},T:function(){var m=this.getMonth();this.setMonth(0);var result=this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/,'$1');this.setMonth(m);return result;},Z:function(){return-this.getTimezoneOffset()*60;},c:function(){return this.format("Y-m-d")+"T"+this.format("H:i:sP");},r:function(){return this.toString();},U:function(){return this.getTime()/1000;}};

<!-- FUNCTIONS TO PERFORM AJAX -->
	var http = false;
	if(navigator.appName == "Microsoft Internet Explorer") {
	  http = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
	  http = new XMLHttpRequest();
	} 
		
	function ajax(url, container, postalcode) {
		  document.getElementById(container).innerHTML = "Vennligst vent, kontakter tjener...";
		  http.open("GET", url, true);
		  http.send(null);
		  http.onreadystatechange=function() {
			if(http.readyState == 4) {
			  if(postalcode == true) {
				  validate_ico = container.replace('place','code');
				  img = document.getElementById('validate_'+validate_ico);
				  if(http.responseText == 'false') {
					  document.getElementById(container).innerHTML = "Ikke funnet";
					  img.src = img.src.replace('check.png', 'stop.png');
				  } else {
					  document.getElementById(container).innerHTML = http.responseText;
					  img.src = img.src.replace('stop.png', 'check.png');
				  }
			  } else 
				  document.getElementById(container).innerHTML = http.responseText;
			}
//		  http.send(null);
		}
	}

function validateTheFunctionOfCP() {
	var thefunction = document.getElementById('toval_p_my_function').value;
	if(thefunction.length == 0)
		alert('Du må skrive hvilken funksjon/rolle du har i innslaget');
	else
		document.getElementById('contactCPform').submit();
}
	
function forgottenDinSidePass(adresse) {
	if(adresse == null)
		adresse = '&email=';
		
	epost = document.getElementById('dinsideEpost').value;
	if(epost.length == 0)
		alert('Du må skrive inn e-postadressen din i e-postfeltet');
	else
		window.location.href = window.location.href + adresse + epost;
}
<!-- -- -- -- -- -- -- -- -- -- -- SMS-PAGE -- -- -- -- -- -- -- -- -- -- -->
function waitAndGo(untill) {
	var now = new Date();
	now = now.getTime();
	now = parseInt(now / 1000);
	untill = parseInt(untill);
	if(now < untill)
		alert('Beklager, du må fortsatt vente i ' + (untill-now) + ' sekunder');
	else
		window.location.href = window.location.href.replace('sms','support');
}
function calcWT() {
	var now = new Date();
	var unix_timestamp = now.getTime()+300000;
	var date = new Date(unix_timestamp);
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	if(minutes < 10) minutes = '0' + minutes;
	
	document.getElementById('WAITTIME').innerHTML = hours + ':' + minutes;
}
function checkCodeEntered() {
	field = document.getElementById('smscode').value;
	if(field.length == 6) 
		return true;
	alert('SMS-koden skal bestå av 6 tegn');
	return false;	
}
<!-- -- -- -- -- -- -- -- -- -- -- SAVE AND DO SOMETHING  -- -- -- -- -- -- -- -- -- -- -->
function showFAQ(divid) {
	div = document.getElementById('detail_' + divid).style;
	if(div.display == 'none')
		div.display = '';
	else
		div.display = 'none';
}

<!-- -- -- -- -- -- -- -- -- -- -- SAVE AND DO SOMETHING  -- -- -- -- -- -- -- -- -- -- -->
function saveAndGo(theForm, theGo, someID) {
	document.getElementById('theGo_input').value = theGo;
	document.getElementById('someID_input').value = someID;
	document.getElementById(theForm).submit();
}

<!-- -- -- -- -- -- -- -- -- -- -- FORM VALIDATION  -- -- -- -- -- -- -- -- -- -- -->
<!-- CONTACT PERSON :: SKIP FUNCTION-FIELD IF IS ONLY CONTACT P-->
	function fixFunction(checkBool) {
		if(checkBool === 0) {
			document.getElementById('p_age_row').style.display = 'none';
			document.getElementById('row_function').style.display =  'none';
			document.getElementById('validate_p_function').src = document.getElementById('validate_p_function').src.replace('stop.png', 'check.png');
			document.getElementById('validate_p_age').src = document.getElementById('validate_p_age').src.replace('stop.png', 'check.png');
			document.getElementById('toval_p_age').selectedIndex = 17;
		} else {
			document.getElementById('p_age_row').style.display = '';
			document.getElementById('row_function').style.display =  '';
			document.getElementById('validate_p_function').src = document.getElementById('validate_p_function').src.replace('check.png', 'stop.png');
			document.getElementById('validate_p_age').src = document.getElementById('validate_p_age').src.replace('check.png', 'stop.png');
			document.getElementById('toval_p_age').selectedIndex = 0;
		}
	}

<!-- FUNCTION TO VALIDATE A FORM ON PAGE LOAD-->
	function validateFormPL() {
		images = document.getElementsByTagName('img');
		for(i=0; i<images.length; i++) {
			img = images[i];
			if(img.id.search('validate_') == 0 && img.src.search('stop.png') != -1) {
				name = img.id.replace('validate_','');
				demand = img.alt;
				status = (img.src.search('check.png') == -1) ? false : true;
				newStatus = eval('check_'+demand + '("'+name+'")');
				<!-- If check is positive, validate -->
				if(newStatus)	img.src = img.src.replace('stop.png', 'check.png');
				else			img.src = img.src.replace('check.png', 'stop.png');
			}
		}
	}
<!-- FUNCTION TO VALIDATE A FORM FOR PROFILE PAGE OR TITLE PAGE -->
	function validateFormPS() {
		images = document.getElementsByTagName('img');
		for(i=0; i<images.length; i++) {
			if(images[i].id.search('validate_') == 0 && images[i].src.search('stop.png') != -1) {
					alert("Et eller flere obligatoriske felt (markert med rødt) mangler. \r\nFyll ut feltene for å fortsette påmeldingen, \r\neller velg \"Tilbake til profilsiden\" for å gjøre dette senere.");
					return false;
			}
		}
		return true;
	}
<!-- FUNCTION TO VALIDATE A FORM -->
	function validateForm() {
		images = document.getElementsByTagName('img');
		for(i=0; i<images.length; i++) {
			if(images[i].id.search('validate_') == 0 && images[i].src.search('stop.png') != -1) {
					alert("Et eller flere obligatoriske felt (markert med rødt) mangler. \r\Du må fylle ut alle feltene for å fortsette påmeldingen");
					return false;
			}
		}
		return true;
	}
<!-- FUNCTION TO CHECK A GIVEN FIELD -->
	function validate(name) {
		img = document.getElementById('validate_'+name);
		demand = img.alt;
		status = (img.src.search('check.png') == -1) ? false : true;
		newStatus = eval('check_'+demand + '("'+name+'")');
		<!-- If check is positive, validate -->
		if(newStatus)	img.src = img.src.replace('stop.png', 'check.png');
		else			img.src = img.src.replace('check.png', 'stop.png');
	}


<!-- FUNCTIONS TO CHECK DIFFERENT CRITERIAS -->
<!--4  postalcode -->
	function check_postalcode(name) {
		var field = trim(document.getElementById('toval_'+name).value);
		if (field.length == 4)
			ajax('http://pamelding.ukm.no/api/postalnumber.php?postalcode=' + field, name.replace('code','place'), true);
		else
			return false;
	}
<!-- 6 letters -->
	function check_sixletters(name) {
		field = trim(document.getElementById('toval_'+name).value);
		return (field.length > 5)
	}
<!-- 3 letters -->
	function check_threeletters(name) {
		field = trim(document.getElementById('toval_'+name).value);
		return (field.length > 2)
	}
<!-- 2 letters -->
	function check_twoletters(name) {
		field = trim(document.getElementById('toval_'+name).value);
		return (field.length > 1)
	}
<!-- 10 letters -->
	function check_tenletters(name) {
		field = trim(document.getElementById('toval_'+name).value);
		return (field.length > 9)
	}
<!-- 20 letters -->
	function check_twentyletters(name) {
		field = trim(document.getElementById('toval_'+name).value);
		return (field.length > 19)
	}

<!-- selected else than 0 -->
	function check_selectedsomething(name) {
		field = document.getElementById('toval_'+name).selectedIndex;
		return (field !== 0);
	}
<!-- valid e-mail address -->
	function check_email(name){
		field = trim(document.getElementById('toval_'+name).value);
		var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
		return (filter.test(field))
	}
<!-- valid cell phone number -->
	function check_cellphone(name) {
		var status = false;
		field = trim(document.getElementById('toval_'+name).value);

		
		// PHONE NUMBER CONTAINS 8 CIFRES
		if(field.length == 8)
			status = true;
		
		// IF 8 LONG, CHECK VALUE IS CELLPHONE
		if(status && (!(90000000 < field && field < 99999999) && !(40000000 < field && field < 50000000)))
			status = false;
		
		// IF IS A FAKE CELL NUMBER
		if(status && field == 44444444)
			status = false;
			
		// RETURN RESULT
		return status;
	}
<!-- valid cell phone number repeated -->
	function check_secondcellphone(name) {
		field = trim(document.getElementById('toval_'+name).value);
		if(field == '') return false;
		return field == document.getElementById('toval_'+name.replace('second','first')).value;
	}
	
function trim(str) {
	str = str.replace(/^\s+/, '');
	for (var i = str.length - 1; i >= 0; i--) {
		if (/\S/.test(str.charAt(i))) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return str;
}
