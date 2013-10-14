<?php
require_once('language/faq/language.php');
$CONTENT = '<h1 style="color: #000;">'.$lang['header'].'</h1>'
        . str_replace(array('#SUPPORTPHONE',
							'#CONTACTP',
							'#CONTACT_PHONE',
							'#CONTACT_P_OR_PNPHONE',
							'#BACK'),
					  array(phone($SUPPORTPHONE),
							isset($pl_contact) ? $pl_contact->g('name').': ' : '',
							isset($pl_contact) ? phone($pl_contact->g('tlf')) : '',
							isset($pl_contact) ? $pl_contact->g('name').' ('.$pl_contact->g('tlf').')' : 'lokalkontakten',
							'javascript:history.go(-1)'),
					  		$lang['text']
						);
