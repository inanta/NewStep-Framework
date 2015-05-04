<?php
require('gettext.inc');

function ns_gettext_init($domain, $locale_dir = null, $encoding = 'UTF-8') {
	if($locale_dir == null) $locale_dir = NS_SYSTEM_PATH . '/locale';

	T_bindtextdomain($domain, $locale_dir);
	T_bind_textdomain_codeset($domain, $encoding);
	T_textdomain($domain);
}
?>