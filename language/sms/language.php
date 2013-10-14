<?php
$lang['entercode'] 	= 'Skriv inn koden du har f&aring;tt p&aring; SMS';
$lang['tekst']		= 'Vi har n&aring; sendt en SMS til #PHONE med en engangskode. <br />Skriv inn koden i feltet nedenfor (vær nøye med store og sm&aring; tegn), og trykk p&aring; knappen.';
$lang['SMS-again']	= 'Koden du skrev inn var feil, vennligst pr&oslash;v igjen';
$lang['SMS-againstill']	= 'Sist du pr&oslash;vde &aring; registrere deg fikk du en SMS-kode som ikke ble skrevet inn. '
					 .'Vennligst skriv inn denne, eller <a href="mailto:support@ukm.no">ta kontakt med support</a>';

$lang['SMS-kode']	= 'SMS-kode:';

$lang['ikkemottatt']= 'Ikke f&aring;tt SMS?';
$lang['sjekk1']	    = '1) Sjekk at vi har brukt riktig nummer (#PHONE). Er nummeret feil, klikk <a href="#LINK">her for å skrive det inn p&aring; nytt</a>'; 
						#LINK is link target
						#PHONE is number
$lang['sjekk2']		= '2) Er nummeret riktig? vent til kl. #WAITTIME og <a href="#LINK">trykk p&aring; denne lenken for &aring; kontakte brukerst&oslash;tte</a>'
					 .'<br /> &nbsp; (Av og til kan det v&aelig;re forsinkelser i nettverket, s&aring; det kan ta litt tid f&oslash;r meldingen kommer frem).'; 
						#LINK is link target
						#WAITTIME is time when possible to click
						  
$lang['submit']		= 'Jeg har skrevet inn engangskoden, fortsett p&aring;melding';

$lang['notsent']	= 'SMS ble ikke sendt til din mobil';
$lang['notsent_why']= 'Dette fordi vi allerede har sendt 3 meldinger til deg, og at du har n&aring;dd grensen for hvor mange SMS vi sender.<br />Om du <a href="#LINK">tar kontakt med support</a> kan vi hjelpe deg.';
						#LINK is link target of $lang['sjekk2']

$lang['confirmSMS']	= 
"Velkommen til UKM! Din kode er #code

Vi er glad du har meldt deg på, lykke til!

Mvh
UKM Norge";
?>