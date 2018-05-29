<?php
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'Mpdf' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Mpdf'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['Mpdf'] = __DIR__ . '/PhpTags.i18n.magic.php';
//	wfWarn(
//		'Deprecated PHP entry point used for PhpTags extension. ' .
//		'Please use wfLoadExtension instead, ' .
//		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
//	);
	return;
} else {
	die( 'This version of the Mpdf extension requires MediaWiki 1.25+' );
}
